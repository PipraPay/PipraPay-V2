# Subdirectory Portability & Dynamic Path Support

## Overview
This update introduces **Dynamic Base URL Support** for PipraPay V2. Previously, the application relied on absolute paths or hardcoded root-level redirects, which caused `404 Not Found` errors and infinite redirect loops when hosted in a subdirectory (e.g., `domain.com/payments/`).

The application now automatically detects its installation depth and adapts all routing, asset loading, and AJAX requests accordingly.

## Key Changes
1. **Dynamic `PP_URL` Constant**: 
   - Implemented in `pp-include/pp-controller.php`.
   - Uses `$_SERVER['SCRIPT_NAME']` to calculate the relative path from the domain root.
   - Example: If installed at `/pipra/`, `PP_URL` becomes `https://yourdomain.com/pipra`.

2. **Refactored Redirects**:
   - Replaced `location.href="/install/"` with relative paths like `location.href="../install/"` or constant-based paths.
   - Core entry points (`index.php`, `admin/index.php`, `install/index.php`) now respect the dynamic base.

3. **Asset & API Link Standardization**:
   - Updated `pp-model.php` and `pp-controller.php` link generation functions.
   - Prepend `PP_URL` to all CSS/JS assets, media files, and API endpoints.

4. **Portability**:
   - The system is now "Write Once, Run Anywhere," supporting Shared Hosting, VPS, and varying directory structures without manual configuration of base URLs.

## Implementation Details
The following files were significantly updated to support this architecture:
- `pp-include/pp-controller.php`: Global constant initialization.
- `index.php`: Root entry point redirect logic.
- `admin/index.php` & `admin/login.php`: Admin panel pathing.
- `install/index.php`: Installer asset and AJAX routing.
- `pp-include/pp-model.php`: Core link generation methods.

## Benefits
- **Ease of Deployment**: Users can simply upload the files to any folder and it works.
- **Standards Compliance**: Follows modern PHP best practices for portable web applications.
- **Stability**: Prevents 404 loops in common hosting environments.
