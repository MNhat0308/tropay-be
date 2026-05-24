import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import UpdatePasswordForm from '../Profile/Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from '../Profile/Partials/UpdateProfileInformationForm';
import { Head } from '@inertiajs/react';

export default function Edit({ user, status }) {
    console.log(user, status)
    return (
        <AuthenticatedLayout
            user={user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>}
        >
            <Head title="Profile" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
