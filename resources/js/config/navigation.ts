import { home } from '@/routes';
import student from '@/routes/student';
import {
    BookOpen,
    Calendar,
    LayoutGrid,
    Settings,
    UserCircle,
    Users,
} from 'lucide-vue-next';

export const defaultNavItems = [
    {
        title: 'Home',
        href: home().url,
        icon: LayoutGrid,
    },
];

export const studentMainNavItems = [
    {
        title: 'Home',
        href: home().url,
    },
    {
        title: 'Dashboard',
        href: student.dashboard().url,
    },
    { title: 'Courses', href: '/student/courses' },
    { title: 'Schedule', href: '/student/schedule' },
    { title: 'Community', href: '/student/community' },
];

export const studentRightNavItems = [
    { title: 'Profile', href: '/student/profile', icon: UserCircle },
    { title: 'Settings', href: '/student/settings', icon: Settings },
];

export const studentBreadcrumbs = [
    { title: 'Home', href: '/student/dashboard' },
];

export const instructorMainNavItems = [
    {
        title: 'Home',
        href: '/',
        icon: LayoutGrid,
    },
    {
        title: 'Dashboard',
        href: '/instructor/dashboard',
        icon: LayoutGrid,
    },
    { title: 'Courses', href: '/instructor/courses', icon: BookOpen },
    { title: 'Schedule', href: '/instructor/schedule', icon: Calendar },
    { title: 'Community', href: '/instructor/community', icon: Users },
];

export const instructorFooterNavItems = [
    { title: 'Profile', href: '/instructor/profile', icon: UserCircle },
    { title: 'Settings', href: '/instructor/settings', icon: Settings },
];

export const instructorBreadcrumbs = [
    { title: 'Home', href: '/instructor/dashboard' },
];
