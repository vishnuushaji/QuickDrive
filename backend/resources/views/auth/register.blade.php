<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
<head>
    <base href="../../../../">
    <title>Metronic - Tailwind CSS Sign Up</title>
    <meta charset="utf-8"/>
    <meta content="follow, index" name="robots"/>
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta content="Sign up page, powered by Tailwind CSS" name="description"/>
    <meta content="@keenthemes" name="twitter:site"/>
    <meta content="@keenthemes" name="twitter:creator"/>
    <meta content="summary_large_image" name="twitter:card"/>
    <meta content="Metronic - Tailwind CSS Sign Up" name="twitter:title"/>
    <meta content="Sign up page, powered by Tailwind CSS" name="twitter:description"/>
    <meta content="{{ asset('assets/media/app/og-image.png') }}" name="twitter:image"/>
    <meta content="https://127.0.0.1:8001/metronic-tailwind-html/demo6/authentication/classic/sign-up" property="og:url"/>
    <meta content="en_US" property="og:locale"/>
    <meta content="website" property="og:type"/>
    <meta content="@keenthemes" property="og:site_name"/>
    <meta content="Metronic - Tailwind CSS Sign Up" property="og:title"/>
    <meta content="Sign up page, powered by Tailwind CSS" property="og:description"/>
    <meta content="{{ asset('assets/media/app/og-image.png') }}" property="og:image"/>

    <link href="{{ asset('assets/media/app/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180"/>
    <link href="{{ asset('assets/media/app/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png"/>
    <link href="{{ asset('assets/media/app/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png"/>
    <link href="{{ asset('assets/media/app/favicon.ico') }}" rel="shortcut icon"/>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet"/>
</head>

<body class="antialiased flex h-full text-base text-foreground bg-background">

<!-- Page -->
<style>
    .page-bg {
        background-image: url('assets/media/images/2600x1200/bg-10.png');
    }
    .dark .page-bg {
        background-image: url('assets/media/images/2600x1200/bg-10-dark.png');
    }
</style>

<div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <div class="kt-card max-w-[370px] w-full">

        <!-- ✅ Corrected Registration Form -->
        <form action="{{ route('register') }}" method="POST" class="kt-card-content flex flex-col gap-5 p-10" id="sign_up_form">
            @csrf

            <div class="text-center mb-2.5">
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Sign up</h3>
                <div class="flex items-center justify-center">
                    <span class="text-sm text-secondary-foreground me-1.5">
                        Already have an Account ?
                    </span>
                    <!-- ✅ Fixed login link -->
                    <a class="text-sm link" href="{{ route('login') }}">
                        Sign In
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <a class="kt-btn kt-btn-outline justify-center" href="#">
                    <img alt="" class="size-3.5 shrink-0" src="assets/media/brand-logos/google.svg"/>
                    Use Google
                </a>
                <a class="kt-btn kt-btn-outline justify-center" href="#">
                    <img alt="" class="size-3.5 shrink-0 dark:hidden" src="assets/media/brand-logos/apple-black.svg"/>
                    <img alt="" class="size-3.5 shrink-0 light:hidden" src="assets/media/brand-logos/apple-white.svg"/>
                    Use Apple
                </a>
            </div>

            <div class="flex items-center gap-2">
                <span class="border-t border-border w-full"></span>
                <span class="text-xs text-secondary-foreground uppercase">or</span>
                <span class="border-t border-border w-full"></span>
            </div>

            <!-- ✅ Added Name -->
            <div class="flex flex-col gap-1">
                <label class="kt-form-label text-mono">Name</label>
                <input class="kt-input" name="name" placeholder="Your Name" type="text" required/>
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label text-mono">Email</label>
                <input class="kt-input" name="email" placeholder="email@email.com" type="email" required/>
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">Password</label>
                <div class="kt-input" data-kt-toggle-password="true">
                    <input name="password" placeholder="Enter Password" type="password" required>
                    <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                            data-kt-toggle-password-trigger="true" type="button">
                        <span class="kt-toggle-password-active:hidden">
                            <i class="ki-filled ki-eye text-muted-foreground"></i>
                        </span>
                        <span class="hidden kt-toggle-password-active:block">
                            <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">Confirm Password</label>
                <div class="kt-input" data-kt-toggle-password="true">
                    <input name="password_confirmation" placeholder="Re-enter Password" type="password" required/>
                    <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                            data-kt-toggle-password-trigger="true" type="button">
                        <span class="kt-toggle-password-active:hidden">
                            <i class="ki-filled ki-eye text-muted-foreground"></i>
                        </span>
                        <span class="hidden kt-toggle-password-active:block">
                            <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                        </span>
                    </button>
                </div>
            </div>

            <label class="kt-checkbox-group">
                <input class="kt-checkbox kt-checkbox-sm" name="check" type="checkbox" value="1"/>
                <span class="kt-checkbox-label">
                    I accept <a class="text-sm link" href="#">Terms & Conditions</a>
                </span>
            </label>

            <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                Sign up
            </button>
        </form>
        <!-- ✅ End Fixed Form -->

    </div>
</div>

<script src="assets/js/core.bundle.js"></script>
<script src="assets/vendors/ktui/ktui.min.js"></script>
<script src="assets/vendors/apexcharts/apexcharts.min.js"></script>
</body>
</html>
