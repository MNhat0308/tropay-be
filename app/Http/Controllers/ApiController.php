<?php

namespace App\Http\Controllers;

use App\Models\BillRoom;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    // 1. Đăng nhập qua Sanctum API
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không chính xác.'], 401);
        }

        // Tạo Token Sanctum
        $token = $user->createToken('tropay-mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    // 2. Lấy danh sách phòng
    public function getRooms()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    // 3. Đồng bộ / Lưu phòng
    public function syncRoom(Request $request)
    {
        $data = $request->all();
        
        if (isset($data['id'])) {
            $room = Room::findOrFail($data['id']);
            $room->update($data);
        } else {
            $room = Room::create($data);
        }

        return response()->json(['success' => true, 'data' => $room]);
    }

    // 4. Lấy danh sách khách thuê
    public function getTenants()
    {
        $tenants = Tenant::all();
        return response()->json($tenants);
    }

    // 5. Đồng bộ / Lưu khách thuê
    public function syncTenant(Request $request)
    {
        $data = $request->all();

        // Chuẩn hóa giới tính và ngày tháng để khớp kiểu dữ liệu cột trong DB
        if (isset($data['dob']) && empty($data['dob'])) {
            $data['dob'] = null;
        }
        if (isset($data['start']) && empty($data['start'])) {
            $data['start'] = null;
        }
        if (isset($data['end']) && empty($data['end'])) {
            $data['end'] = null;
        }

        if (isset($data['id'])) {
            $tenant = Tenant::findOrFail($data['id']);
            $tenant->update($data);
        } else {
            $tenant = Tenant::create($data);
        }

        // Tự động cập nhật trạng thái phòng khi thêm khách
        if ($tenant->room_id) {
            $room = Room::find($tenant->room_id);
            if ($room && $room->status !== 'occupied') {
                $room->update(['status' => 'occupied']);
            }
        }

        return response()->json(['success' => true, 'data' => $tenant]);
    }

    // 6. Lấy danh sách hóa đơn
    public function getBills()
    {
        $bills = BillRoom::with('room')->get();
        return response()->json($bills);
    }

    // 7. Đồng bộ / Lưu hóa đơn tính tiền
    public function syncBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'rent_month' => 'required|integer',
            'old_electric' => 'required',
            'new_electric' => 'required',
            'old_water' => 'required',
            'new_water' => 'required',
            'price_room' => 'required',
            'price_electric' => 'required',
            'price_water' => 'required',
            'price_garbage' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Lỗi xác thực dữ liệu hóa đơn', 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Tự tính lại lượng tiêu thụ và tổng cộng nếu frontend chưa gửi
        $elecCons = max(0, floatval($data['new_electric']) - floatval($data['old_electric']));
        $waterCons = max(0, floatval($data['new_water']) - floatval($data['old_water']));
        
        $data['electric_consumption'] = $elecCons;
        $data['water_consumption'] = $waterCons;
        
        // Tính toán tiền điện bậc thang lũy tiến phía máy chủ (Tier 1 = price - 500, Tier 2 = price)
        $priceElec = floatval($data['price_electric']);
        $tier1Price = $priceElec - 500;
        $tier2Price = $priceElec;
        
        if ($elecCons > 100) {
            $elecBill = (100 * $tier1Price) + (($elecCons - 100) * $tier2Price);
        } else {
            $elecBill = $elecCons * $tier1Price;
        }
        
        $totalPrice = $elecBill + 
                     ($waterCons * floatval($data['price_water'])) + 
                     floatval($data['price_room']) + 
                     floatval($data['price_garbage']);

        $data['total_price'] = $totalPrice;
        $data['at'] = $data['at'] ?? now()->toIso8601String();

        if (isset($data['id'])) {
            $bill = BillRoom::findOrFail($data['id']);
            $bill->update($data);
        } else {
            $bill = BillRoom::create($data);
        }

        return response()->json(['success' => true, 'data' => $bill]);
    }
}
