<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { 
    BookOpen, 
    MessageSquare, 
    Layers, 
    Trophy, 
    ArrowRight, 
    GraduationCap, 
    CheckCircle, 
    Users, 
    Star,
    Compass,
    Code,
    Palette,
    BarChart,
    Briefcase
} from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome to LMS">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <div class="relative min-h-screen w-full bg-white text-neutral-900 transition-colors duration-300 dark:bg-neutral-950 dark:text-neutral-50 font-sans overflow-x-hidden">
        <!-- Diagonal Cross Top Right Fade Grid Background -->
        <div
            class="absolute inset-0 pointer-events-none z-0"
            style="
                background-image: 
                    linear-gradient(45deg, transparent 49%, var(--grid-color) 49%, var(--grid-color) 51%, transparent 51%),
                    linear-gradient(-45deg, transparent 49%, var(--grid-color) 49%, var(--grid-color) 51%, transparent 51%)
                ;
                background-size: 40px 40px;
                -webkit-mask-image: radial-gradient(ellipse 80% 80% at 0% 0%, #000 50%, transparent 90%);
                mask-image: radial-gradient(ellipse 80% 80% at 0% 0%, #000 50%, transparent 90%);
            "
        />

        <!-- Glassmorphism Navigation Bar -->
        <header class="sticky top-0 z-50 w-full border-b border-neutral-200/50 bg-white/85 backdrop-blur-md dark:border-neutral-800/50 dark:bg-neutral-950/85">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="flex aspect-square size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-sm shadow-blue-500/20">
                        <GraduationCap class="size-5" />
                    </div>
                    <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary to-blue-600 dark:from-neutral-50 dark:to-blue-400">LMS</span>
                </div>

                <!-- Middle Nav Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-neutral-600 dark:text-neutral-300">
                    <a href="#features" class="hover:text-primary transition-colors">Features</a>
                    <a href="#categories" class="hover:text-primary transition-colors">Categories</a>
                    <a href="#stats" class="hover:text-primary transition-colors">Impact</a>
                    <a href="#testimonials" class="hover:text-primary transition-colors">Stories</a>
                </nav>

                <!-- Auth Actions -->
                <div class="flex items-center gap-4">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="dashboard()"
                            class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-5 text-sm font-medium text-primary-foreground shadow-xs hover:bg-primary/95 transition-all duration-200 active:scale-[0.98]"
                        >
                            Go to Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-medium text-neutral-600 hover:text-neutral-950 dark:text-neutral-400 dark:hover:text-white transition-colors"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-5 text-sm font-medium text-primary-foreground shadow-xs hover:bg-primary/95 transition-all duration-200 active:scale-[0.98]"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative z-10 mx-auto max-w-7xl px-6 pt-10 pb-20 lg:px-8 lg:pt-16 lg:pb-32">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Content -->
                <div class="lg:col-span-7 flex flex-col justify-center text-left">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 self-start rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-primary dark:bg-blue-950/40 dark:text-blue-400 mb-6 border border-blue-200/50 dark:border-blue-800/30">
                        <span class="flex h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                        Flexible Learning Ecosystem
                    </div>

                    <!-- Headline -->
                    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-neutral-950 dark:text-neutral-50 leading-[1.15]">
                        Master New Skills, <br />
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary via-blue-600 to-indigo-500 dark:from-primary dark:via-blue-400 dark:to-indigo-300">
                            On Your Own Terms
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="mt-6 text-lg text-neutral-600 dark:text-neutral-400 max-w-xl leading-relaxed">
                        Access high-quality interactive modules, test your comprehension with live quizzes, collaborate with colleagues, and view your complete trajectory in real time.
                    </p>

                    <!-- CTAs -->
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <Link
                            v-if="!$page.props.auth.user && canRegister"
                            :href="register()"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-6 text-base font-semibold text-primary-foreground shadow-md hover:bg-primary/95 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                        >
                            Create Free Account
                        </Link>
                        <Link
                            v-else-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-6 text-base font-semibold text-primary-foreground shadow-md hover:bg-primary/95 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                        >
                            Access Classroom
                        </Link>
                        <a
                            href="#features"
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-neutral-200 bg-white/50 px-6 text-base font-semibold text-neutral-700 backdrop-blur-xs hover:bg-neutral-50 hover:text-neutral-950 dark:border-neutral-800 dark:bg-neutral-900/50 dark:text-neutral-300 dark:hover:bg-neutral-850 dark:hover:text-white transition-all duration-200"
                        >
                            Explore Features
                        </a>
                    </div>
                </div>

                <!-- Hero Image Container (Bottom Right) -->
                <div class="lg:col-span-5 relative mt-8 lg:mt-0 flex justify-center lg:justify-end">
                    <!-- Blur Backdrop Glow -->
                    <div class="absolute -inset-4 -z-10 rounded-full bg-blue-400/25 dark:bg-blue-600/10 blur-3xl"></div>
                    
                    <div class="relative w-full max-w-[450px] overflow-hidden rounded-2xl border border-neutral-200/60 bg-white/40 p-4 shadow-xl backdrop-blur-xs dark:border-neutral-800/40 dark:bg-neutral-900/45 animate-[float_6s_ease-in-out_infinite]">
                        <img 
                            src="/images/hero-img.png" 
                            alt="Learning Management System Concept" 
                            class="w-full h-auto object-cover rounded-xl"
                        />
                        <!-- Subtle floating card -->
                        <div class="absolute bottom-6 left-6 right-6 rounded-xl border border-white/60 bg-white/80 p-4 shadow-md backdrop-blur-md dark:border-neutral-800/60 dark:bg-neutral-900/80 flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-primary dark:bg-blue-900/55 dark:text-blue-400">
                                <Trophy class="size-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">LMS Activity</p>
                                <p class="text-sm font-bold text-neutral-900 dark:text-neutral-100 truncate">124 students certified today</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative z-10 border-t border-neutral-200/50 bg-neutral-50/50 py-20 dark:border-neutral-800/50 dark:bg-neutral-900/20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-primary">Everything you need</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl text-neutral-950 dark:text-neutral-50">
                        Tailored for Modern Learning
                    </p>
                    <p class="mt-4 text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Say goodbye to static files. Our interactive platform brings learning materials to life, boosting comprehension and performance.
                    </p>
                </div>

                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 lg:max-w-none lg:grid-cols-4">
                        <!-- Feature 1 -->
                        <div class="flex flex-col rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:border-primary/45 transition-colors group">
                            <dt class="flex items-center gap-x-3 text-base font-semibold text-neutral-950 dark:text-neutral-50">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400 group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-200">
                                    <BookOpen class="size-5" />
                                </div>
                                Interactive Modules
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                <p class="flex-auto">Browse through structure-rich chapters, take inline code samples, and mark off items as you complete them.</p>
                            </dd>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="flex flex-col rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:border-primary/45 transition-colors group">
                            <dt class="flex items-center gap-x-3 text-base font-semibold text-neutral-950 dark:text-neutral-50">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400 group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-200">
                                    <Trophy class="size-5" />
                                </div>
                                Dynamic Quizzes
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                <p class="flex-auto">Practice in real time. Our modules feature customizable quizzes with direct scoring pipelines and detailed answer descriptions.</p>
                            </dd>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex flex-col rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:border-primary/45 transition-colors group">
                            <dt class="flex items-center gap-x-3 text-base font-semibold text-neutral-950 dark:text-neutral-50">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400 group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-200">
                                    <MessageSquare class="size-5" />
                                </div>
                                Social Discussions
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                <p class="flex-auto">Exchange ideas with classmates and teachers. Ask queries, answer other posts, and review code blocks on our discussion forums.</p>
                            </dd>
                        </div>

                        <!-- Feature 4 -->
                        <div class="flex flex-col rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:border-primary/45 transition-colors group">
                            <dt class="flex items-center gap-x-3 text-base font-semibold text-neutral-950 dark:text-neutral-50">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400 group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-200">
                                    <Layers class="size-5" />
                                </div>
                                Progress Tracking
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                <p class="flex-auto">Follow your personal roadmap. Dashboard analytics show your progression milestones, course certificates, and test grades.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- Course Categories Showcase -->
        <section id="categories" class="relative z-10 py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-primary">Discover your path</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl text-neutral-950 dark:text-neutral-50">
                        Popular Topics Available
                    </p>
                    <p class="mt-4 text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Explore courses curated by industry professionals. Learn practical skills that get you noticed.
                    </p>
                </div>

                <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-6 sm:mt-20 lg:max-w-none lg:grid-cols-3">
                    <!-- Category 1 -->
                    <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400">
                                <Code class="size-6" />
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-neutral-950 dark:text-neutral-50">Software Development</h3>
                            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">Full-stack web apps, mobile coding in Kotlin/Swift, systems development, and database engineering.</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between text-xs font-semibold text-primary">
                            <span>120+ courses</span>
                            <span class="inline-flex items-center gap-1 group-hover:translate-x-1.5 transition-transform duration-200">
                                Explore <ArrowRight class="size-3.5" />
                            </span>
                        </div>
                    </div>

                    <!-- Category 2 -->
                    <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400">
                                <Palette class="size-6" />
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-neutral-950 dark:text-neutral-50">Creative Design</h3>
                            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">User experience research, UI layout frameworks, graphic vector artwork, and digital illustration tools.</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between text-xs font-semibold text-primary">
                            <span>85+ courses</span>
                            <span class="inline-flex items-center gap-1 group-hover:translate-x-1.5 transition-transform duration-200">
                                Explore <ArrowRight class="size-3.5" />
                            </span>
                        </div>
                    </div>

                    <!-- Category 3 -->
                    <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200/50 bg-white p-6 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50 hover:-translate-y-1 transition-all duration-300">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-primary dark:bg-blue-950/40 dark:text-blue-400">
                                <BarChart class="size-6" />
                            </div>
                            <h3 class="mt-4 text-lg font-bold text-neutral-950 dark:text-neutral-50">Data & Analytics</h3>
                            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">Data science scripting, machine learning models, statistical analysis, and query logic structures.</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between text-xs font-semibold text-primary">
                            <span>64+ courses</span>
                            <span class="inline-flex items-center gap-1 group-hover:translate-x-1.5 transition-transform duration-200">
                                Explore <ArrowRight class="size-3.5" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="stats" class="relative z-10 border-y border-neutral-200/50 bg-neutral-50/50 py-16 dark:border-neutral-800/50 dark:bg-neutral-900/20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-4xl font-bold tracking-tight text-primary sm:text-5xl">15k+</p>
                        <p class="mt-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400">Active Students</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold tracking-tight text-primary sm:text-5xl">350+</p>
                        <p class="mt-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400">Online Courses</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold tracking-tight text-primary sm:text-5xl">94%</p>
                        <p class="mt-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400">Completion Rate</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold tracking-tight text-primary sm:text-5xl">4.8★</p>
                        <p class="mt-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400">Average Rating</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Stories / Testimonials Section -->
        <section id="testimonials" class="relative z-10 py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center mb-16">
                    <h2 class="text-base font-semibold leading-7 text-primary">Student Stories</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl text-neutral-950 dark:text-neutral-50">
                        Loved by Students Worldwide
                    </p>
                    <p class="mt-4 text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Read how our interactive LMS transformed careers and built confidence.
                    </p>
                </div>

                <div class="mx-auto grid max-w-2xl grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3">
                    <!-- Testimonial 1 -->
                    <div class="rounded-2xl border border-neutral-200/50 bg-white p-8 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50">
                        <div class="flex items-center gap-1 text-primary">
                            <Star class="size-4 fill-current" v-for="i in 5" :key="i" />
                        </div>
                        <blockquote class="mt-4 text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed">
                            "The interactive quizzes here make a big difference. Instead of just reading, I was solving problems continuously. I landed a software engineer position within three months!"
                        </blockquote>
                        <div class="mt-6 flex items-center gap-4 border-t border-neutral-100 pt-6 dark:border-neutral-800/50">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-primary dark:bg-blue-900/40 dark:text-blue-400">
                                EM
                            </div>
                            <div>
                                <p class="text-sm font-bold text-neutral-950 dark:text-neutral-50">Emily Miller</p>
                                <p class="text-xs text-neutral-500">Junior Web Developer</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="rounded-2xl border border-neutral-200/50 bg-white p-8 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50">
                        <div class="flex items-center gap-1 text-primary">
                            <Star class="size-4 fill-current" v-for="i in 5" :key="i" />
                        </div>
                        <blockquote class="mt-4 text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed">
                            "I loved the discussion forum. I had some problems debugging my CSS modules, and the teacher responded to my code block in an hour. Very supportive community."
                        </blockquote>
                        <div class="mt-6 flex items-center gap-4 border-t border-neutral-100 pt-6 dark:border-neutral-800/50">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-primary dark:bg-blue-900/40 dark:text-blue-400">
                                JC
                            </div>
                            <div>
                                <p class="text-sm font-bold text-neutral-950 dark:text-neutral-50">Jason Chen</p>
                                <p class="text-xs text-neutral-500">UX Design Student</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="rounded-2xl border border-neutral-200/50 bg-white p-8 shadow-xs dark:border-neutral-800/50 dark:bg-neutral-900/50">
                        <div class="flex items-center gap-1 text-primary">
                            <Star class="size-4 fill-current" v-for="i in 5" :key="i" />
                        </div>
                        <blockquote class="mt-4 text-sm text-neutral-600 dark:text-neutral-300 leading-relaxed">
                            "The timeline dashboard helps me structure my weeks perfectly. I can see what lessons are pending and practice query layouts directly. Highly recommend it."
                        </blockquote>
                        <div class="mt-6 flex items-center gap-4 border-t border-neutral-100 pt-6 dark:border-neutral-800/50">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-primary dark:bg-blue-900/40 dark:text-blue-400">
                                SR
                            </div>
                            <div>
                                <p class="text-sm font-bold text-neutral-950 dark:text-neutral-50">Sarah Rodriguez</p>
                                <p class="text-xs text-neutral-500">Data Analyst Analyst</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative z-10 py-16 px-6 lg:px-8">
            <div class="mx-auto max-w-5xl rounded-3xl bg-gradient-to-tr from-blue-700 via-primary to-indigo-800 px-8 py-16 shadow-xl text-center text-white relative overflow-hidden">
                <!-- Inner glow blobs -->
                <div class="absolute -top-24 -left-24 size-48 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-24 -right-24 size-48 rounded-full bg-white/10 blur-2xl"></div>

                <div class="relative z-10 max-w-2xl mx-auto">
                    <h2 class="text-3xl font-extrabold sm:text-4xl">Ready to Start Learning?</h2>
                    <p class="mt-4 text-lg text-blue-100 leading-relaxed">
                        Create an account today and access dozens of interactive courses with live modules and social group discussions.
                    </p>
                    <div class="mt-8 flex justify-center gap-4">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-white px-6 text-base font-semibold text-primary hover:bg-neutral-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                        >
                            Sign Up Free
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-white px-6 text-base font-semibold text-primary hover:bg-neutral-50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                        >
                            Enter Classroom
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Small Footer -->
        <footer class="relative z-10 border-t border-neutral-200/50 bg-white py-8 dark:border-neutral-800/50 dark:bg-neutral-950">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-neutral-500 dark:text-neutral-400">
                <!-- Copyright & Name -->
                <div>
                    &copy; 2026 LMS. All rights reserved.
                </div>

                <!-- Theme Switcher -->
                <div>
                    <AppearanceTabs />
                </div>

                <!-- Legal links -->
                <div class="flex gap-4">
                    <a href="#" class="hover:underline">Terms of Service</a>
                    <a href="#" class="hover:underline">Privacy Policy</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}
</style>
