<script setup lang="ts">
import { useA11yPrefs } from '@/composables/useA11yPrefs';

const { prefs, updatePref } = useA11yPrefs();
</script>

<template>
    <section aria-labelledby="a11y-prefs-heading">
        <header>
            <h2 id="a11y-prefs-heading" class="text-lg font-medium text-gray-900">
                Accessibility Preferences
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Customise how Eduno looks and behaves for your comfort.
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <!-- Dyslexia-friendly font -->
            <div class="flex items-center justify-between">
                <div>
                    <label for="dyslexia-font" class="font-medium text-gray-700 text-sm">
                        Dyslexia-friendly font
                    </label>
                    <p id="dyslexia-font-desc" class="text-xs text-gray-500">
                        Uses OpenDyslexic to improve readability.
                    </p>
                </div>
                <button
                    id="dyslexia-font"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.dyslexia_font === true"
                    aria-describedby="dyslexia-font-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    :class="prefs?.dyslexia_font ? 'bg-indigo-600' : 'bg-gray-200'"
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
                    <label for="reduced-motion" class="font-medium text-gray-700 text-sm">
                        Reduce motion
                    </label>
                    <p id="reduced-motion-desc" class="text-xs text-gray-500">
                        Disables animations and transitions.
                    </p>
                </div>
                <button
                    id="reduced-motion"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.reduced_motion === true"
                    aria-describedby="reduced-motion-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    :class="prefs?.reduced_motion ? 'bg-indigo-600' : 'bg-gray-200'"
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
                    <label for="high-contrast" class="font-medium text-gray-700 text-sm">
                        High contrast
                    </label>
                    <p id="high-contrast-desc" class="text-xs text-gray-500">
                        Increases visual contrast across the interface.
                    </p>
                </div>
                <button
                    id="high-contrast"
                    type="button"
                    role="switch"
                    :aria-checked="prefs?.high_contrast === true"
                    aria-describedby="high-contrast-desc"
                    class="relative inline-flex h-6 w-11 items-center rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    :class="prefs?.high_contrast ? 'bg-indigo-600' : 'bg-gray-200'"
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
</template>
