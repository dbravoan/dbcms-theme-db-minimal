import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: '.',
    testMatch: 'content-region.spec.mjs',
    workers: 1,
    timeout: 30000,
    outputDir: process.env.DBCMS_BROWSER_RESULTS || '/tmp/dbcms-db-minimal-browser-results',
    reporter: 'list',
    use: {
        reducedMotion: 'reduce',
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
        launchOptions: process.env.DBCMS_BROWSER_CHROME ? { executablePath: process.env.DBCMS_BROWSER_CHROME } : {},
    },
});
