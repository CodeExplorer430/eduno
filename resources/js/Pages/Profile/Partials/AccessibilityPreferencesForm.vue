<script setup lang="ts">
import { useA11yPrefs } from '@/composables/useA11yPrefs';

const { prefs, updatePref } = useA11yPrefs();
</script>

<template>
    <section aria-labelledby="a11y-prefs-heading">
        <header>
            <h2 id="a11y-prefs-heading" class="text-lg font-medium text-gray-900 dark:text-white">
                Accessibility Preferences
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Customise how Eduno looks and behaves for your comfort.
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <!-- Dyslexia-friendly font -->
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="dyslexia-font"
                        class="font-medium text-gray-700 text-sm dark:text-gray-300"
                    >
                        Dyslexia-friendly font
                    </label>
                    <p id="dyslexia-font-desc" class="text-xs text-gray-500 dark:text-slate-400">
                        Uses OpenDyslexic to improve readability.
                    </p>
                </div>
                <button
                    id="dyslexia-font"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.dyslexia_font === true"
                    aria-describedby="dyslexia-font-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    :class="prefs?.dyslexia_font ? 'bg-blue-600' : 'bg-gray-200'"
                    @click="updatePref('dyslexia_font', !prefs?.dyslexia_font)"
                >
                    <span class="sr-only">Toggle dyslexia-friendly font</span>
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        :class="prefs?.dyslexia_font ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>

            <!-- Reduced motion -->
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="reduced-motion"
                        class="font-medium text-gray-700 text-sm dark:text-gray-300"
                    >
                        Reduce motion
                    </label>
                    <p id="reduced-motion-desc" class="text-xs text-gray-500 dark:text-slate-400">
                        Disables animations and transitions.
                    </p>
                </div>
                <button
                    id="reduced-motion"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.reduced_motion === true"
                    aria-describedby="reduced-motion-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    :class="prefs?.reduced_motion ? 'bg-blue-600' : 'bg-gray-200'"
                    @click="updatePref('reduced_motion', !prefs?.reduced_motion)"
                >
                    <span class="sr-only">Toggle reduced motion</span>
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        :class="prefs?.reduced_motion ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>

            <!-- High contrast -->
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="high-contrast"
                        class="font-medium text-gray-700 text-sm dark:text-gray-300"
                    >
                        High contrast
                    </label>
                    <p id="high-contrast-desc" class="text-xs text-gray-500 dark:text-slate-400">
                        Increases visual contrast across the interface.
                    </p>
                </div>
                <button
                    id="high-contrast"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.high_contrast === true"
                    aria-describedby="high-contrast-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    :class="prefs?.high_contrast ? 'bg-blue-600' : 'bg-gray-200'"
                    @click="updatePref('high_contrast', !prefs?.high_contrast)"
                >
                    <span class="sr-only">Toggle high contrast</span>
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        :class="prefs?.high_contrast ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>
        </div>
    </section>

    <section aria-labelledby="notification-prefs-heading" class="mt-8">
        <header>
            <h2
                id="notification-prefs-heading"
                class="text-lg font-medium text-gray-900 dark:text-white"
            >
                Notification Preferences
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Choose which email notifications you want to receive from Eduno.
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <!-- Email notifications -->
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="email-notifications"
                        class="font-medium text-gray-700 text-sm dark:text-gray-300"
                    >
                        Email notifications
                    </label>
                    <p
                        id="email-notifications-desc"
                        class="text-xs text-gray-500 dark:text-slate-400"
                    >
                        Receive deadline reminders and grade release alerts via email.
                    </p>
                </div>
                <button
                    id="email-notifications"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.email_notifications !== false"
                    aria-describedby="email-notifications-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    :class="prefs?.email_notifications !== false ? 'bg-blue-600' : 'bg-gray-200'"
                    @click="updatePref('email_notifications', prefs?.email_notifications === false)"
                >
                    <span class="sr-only">Toggle email notifications</span>
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        :class="
                            prefs?.email_notifications !== false ? 'translate-x-6' : 'translate-x-1'
                        "
                    />
                </button>
            </div>

            <!-- Email digest -->
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="email-digest"
                        class="font-medium text-gray-700 text-sm dark:text-gray-300"
                    >
                        Email digest for announcements
                    </label>
                    <p id="email-digest-desc" class="text-xs text-gray-500 dark:text-slate-400">
                        Receive a daily digest of instructor announcements instead of individual
                        emails.
                    </p>
                </div>
                <button
                    id="email-digest"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.email_digest === true"
                    aria-describedby="email-digest-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    :class="prefs?.email_digest ? 'bg-blue-600' : 'bg-gray-200'"
                    @click="updatePref('email_digest', !prefs?.email_digest)"
                >
                    <span class="sr-only">Toggle email digest</span>
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                        :class="prefs?.email_digest ? 'translate-x-6' : 'translate-x-1'"
                    />
                </button>
            </div>
        </div>
    </section>
</template>
