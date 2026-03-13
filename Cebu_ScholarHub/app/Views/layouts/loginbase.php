<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Authentication') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes floatBlob {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-18px) translateX(8px); }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeScale {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-floatBlob {
            animation: floatBlob 6s ease-in-out infinite;
        }

        .animate-fadeUp {
            animation: fadeUp 0.8s ease-out;
        }

        .animate-fadeScale {
            animation: fadeScale 0.45s ease-out;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-white">

    <div class="relative min-h-screen overflow-hidden">

        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950"></div>

        <div class="absolute inset-0 opacity-30">
            <div class="absolute -top-20 -left-16 h-72 w-72 rounded-full bg-cyan-400 blur-3xl animate-floatBlob"></div>
            <div class="absolute top-1/4 -right-10 h-80 w-80 rounded-full bg-violet-500 blur-3xl animate-floatBlob"></div>
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-fuchsia-500 blur-3xl animate-floatBlob"></div>
        </div>

        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:40px_40px]"></div>

        <!-- Main -->
        <div class="relative z-10 min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-10 items-center">

                <!-- Left panel -->
                <div class="hidden lg:block animate-fadeUp">
                    <div class="max-w-xl">
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1 text-sm text-slate-200 backdrop-blur-xl">
                            Secure Verification Portal
                        </span>

                        <h1 class="mt-6 text-5xl font-bold leading-tight">
                            Verify your
                            <span class="bg-gradient-to-r from-cyan-300 via-blue-400 to-violet-400 bg-clip-text text-transparent">
                                account access
                            </span>
                        </h1>

                        <p class="mt-5 text-lg leading-relaxed text-slate-300">
                            A modern authentication screen with OTP verification, smooth effects,
                            and a premium glass layout for your ScholarHub login system.
                        </p>

                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl">
                                <p class="text-sm text-slate-300">Feature</p>
                                <h3 class="mt-1 text-lg font-semibold">OTP Verification</h3>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl">
                                <p class="text-sm text-slate-300">Style</p>
                                <h3 class="mt-1 text-lg font-semibold">Glass UI</h3>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl">
                                <p class="text-sm text-slate-300">Protection</p>
                                <h3 class="mt-1 text-lg font-semibold">Secure Access</h3>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl">
                                <p class="text-sm text-slate-300">Experience</p>
                                <h3 class="mt-1 text-lg font-semibold">Modern Layout</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right panel -->
                <div class="animate-fadeScale">
                    <div class="mx-auto w-full max-w-md rounded-[28px] border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-2xl sm:p-8">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>