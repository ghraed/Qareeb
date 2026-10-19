# Feature test coverage

`npm run test:all` runs the PHPUnit unit/feature suites, Node's JavaScript tests,
and the image concurrency integration test. The render-ui.php script generates
manual visual-review fixtures; it is not an automated assertion suite.

PHPUnit uses a fresh in-memory SQLite database by default. Image tests use fake
storage, and the concurrency test uses disposable SQLite and files under `/tmp`.
To check MySQL-specific behavior, explicitly select a dedicated disposable MySQL
database through `DB_CONNECTION` and `DB_DATABASE` when running `composer test`.
SQLite results do not establish compatibility with every MySQL-specific behavior.

| Feature | Tests |
| --- | --- |
| Provider registration, login, logout, account status | BusinessAuthTest, ProviderAuthenticationTest, ProviderPreregistrationTest, OptionalIdentityTest |
| Seeker registration, login, logout | SeekerAuthenticationTest, OptionalIdentityTest |
| Admin authentication and session handling | AdminAuthenticationTest |
| Terms acceptance and consent version | TermsAcceptanceTest |
| Navigation and mobile menu focus/scroll | SiteNavigationTest, js/site-navigation.test.mjs |
| Provider dashboard and profile completion | ProviderDashboardTest, ProviderCompletionTest |
| Provider details, categories, location coverage | ProviderProfileEditingTest, BusinessProfileFeatureTest, CategoryChangesTest, js/provider-location.test.mjs |
| Provider profile, brand and gallery images | BusinessProfileFeatureTest, ProviderImageSafetyTest, integration/image-concurrency.py |
| Public profiles, contact links and hiring badges | BusinessProfileFeatureTest, DirectProviderContactTest, HiringBadgeTest |
| Provider availability and browser request failures | ProviderAvailabilityTest, js/availability-toggle.test.mjs |
| Directory filters, location validation and category dialogs | ProviderDirectoryTest, js/provider-filter.test.mjs |
| Infinite scrolling and retries | InfiniteScrollTest, ProviderDirectoryTest, js/infinite-scroll.test.mjs |
| Reviews: create, update, delete, permissions, validation, ratings, pagination and escaped content | BusinessProfileFeatureTest, ProviderReviewLifecycleTest |
| Private seeker profile, dashboard and completion | SeekerProfileTest, SeekerDashboardTest, SeekerCompletionTest |
| Provider approval, rejection, suspension and reactivation | ProviderModerationTest, ProviderPreregistrationTest |
| Private complaints, duplicate protection and admin review | PrivateComplaintTest |
| Image privacy reports, moderation, safe removal and rate limits | ImagePrivacyReportTest |
| Success/error feedback and toast timing | ToastFeedbackTest, js/toast.test.mjs |
| Guest redirects, role boundaries, restricted provider operations, session-authenticated API privacy and health endpoint | FeatureAccessTest |
| Demo data and explicit cleanup | ProviderSeederTest |
| Form rendering and image URLs | Unit/FormInputComponentTest, Unit/EditBusinessProfileViewTest, Unit/BusinessProfileUrlTest |

PHP feature files are under `tests/Feature` unless otherwise indicated. This is
a feature inventory, not a line-coverage measurement or a full browser end-to-end
suite. JavaScript interaction tests use browser API doubles.
