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
    BookOpen,
    Calendar,
    GraduationCap,
    ShoppingBag,
} from 'lucide-vue-next';

export const defaultNavItems = [
    {
        title: 'Home',
        href: home().url,
        icon: LayoutGrid,
    },
];

// Student Navigation
export const studentMainNavItems = [
    {
        title: 'Dashboard',
        href: student.dashboard().url,
        icon: Grid2X2Plus,
    },
    {
        title: 'Course Catalog',
        href: '/courses',
        icon: ShoppingBag,
    },
    {
        title: 'My Courses',
        href: '/student/courses',
        icon: BookOpen,
    },
    {
        title: 'Schedule',
        href: '/student/schedule',
        icon: Calendar,
    },
];

export const studentRightNavItems = [
    { title: 'Dashboard', href: student.dashboard().url, icon: Grid2X2Plus },
    { title: 'My Courses', href: '/student/courses', icon: BookOpen },
];

export const studentBreadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Student Dashboard', href: student.dashboard().url },
];

// Instructor Navigation
export const instructorMainNavItems = [
    {
        title: 'Dashboard',
        href: instructor.dashboard().url,
        icon: Grid2X2Plus,
    },
    {
        title: 'Courses',
        href: instructor.courses.index().url,
        icon: LibraryBig,
    },
    {
        title: 'Schedule',
        href: '/instructor/schedule',
        icon: Calendar,
    },
];

export const instructorFooterNavItems = [
    { title: 'Home', href: home().url, icon: Home },
    { title: 'Settings', href: instructor.settings().url, icon: Settings },
];

export const instructorBreadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Instructor Dashboard', href: instructor.dashboard().url },
];

// Public Navigation (for course catalog, etc.)
export const publicMainNavItems = [
    {
        title: 'Home',
        href: home().url,
        icon: Home,
    },
    {
        title: 'Course Catalog',
        href: '/courses',
        icon: LibraryBig,
    },
];
