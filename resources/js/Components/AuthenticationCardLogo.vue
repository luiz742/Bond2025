<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link href="/">
        <img src="/images/logo.png" alt="Logo" class="block dark:hidden h-14 w-auto" />
        <img src="/images/logo-white.png" alt="Logo White" class="hidden dark:block h-14 w-auto" />
    </Link>
</template>
