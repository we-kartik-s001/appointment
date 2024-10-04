# vaahcms-theme-authentication
Authentication Theme for VaahCMS


#### To Run Theme Dusk Test:
- Change path of dusk in `phpunit.dusk.xml` to following:
```xml
...
<directory suffix="Test.php">./VaahCms/Themes/Authentication/Tests/Browser</directory>
...
```

- Then run `php artisan dusk`