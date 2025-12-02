import { home } from '@/routes';

import instructor from '@/routes/instructor';
import student from '@/routes/student';

import {
    CornerDownLeft,
    CornerDownRight,
    CornerUpLeft,
    Grid2X2Plus,
    Home,
    LayoutGrid,
    LibraryBig,
    Settings,
} from 'lucide-vue-next';

export const defaultNavItems = [
    {
        title: 'Home',
        href: home().url,
        icon: LayoutGrid,
    },
];

export const studentMainNavItems = [
    //no icons change later
    {
        title: 'Home',
        href: home().url,
    },
    {
        title: 'Dashboard',
        href: student.dashboard().url,
    },
    {
        title: 'Student1',
        href: '/student/courses',
    },
    {
        title: 'student2',
        href: '/student/schedule',
    },
    {
        title: 'student3',
        href: '/student/community',
    },
];

export const studentRightNavItems = [
    { title: 'Dashboard', href: student.dashboard().url, icon: LibraryBig },
    //{ title: 'Settings', href: student.dashboard().url, icon: Settings },
];

export const studentBreadcrumbs = [
    { title: 'Home', href: '/student/dashboard' },
];

export const instructorMainNavItems = [
    {
        title: 'Dashboard',
        href: instructor.dashboard().url,
        icon: Grid2X2Plus,
    },
    {
        title: 'Instructor1',
        href: '/instructor/courses',
        icon: CornerUpLeft,
    },
    {
        title: 'Instructor2',
        href: '/instructor/schedule',
        icon: CornerDownLeft,
    },
    {
        title: 'Instructor3',
        href: '/instructor/community',
        icon: CornerDownRight,
    },
];

export const instructorFooterNavItems = [
    { title: 'Home', href: home().url, icon: Home },
    { title: 'Settings', href: instructor.settings().url, icon: Settings },
];

export const instructorBreadcrumbs = [
    { title: 'Home', href: '/instructor/dashboard' },
];
