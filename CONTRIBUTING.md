# Contributing to PipraPay V2

Thank you for your interest in contributing to PipraPay! We welcome contributions that improve the stability, portability, and security of this payment gateway.

## Submission Guidelines

1. **Focus on Portability**: Ensure any new features or fixes maintain compatibility with subdirectory installations. Always use the `PP_URL` constant for absolute internal links.
2. **Coding Standards**: Follow standard PHP PSR guidelines. Use `mysqli_real_escape_string` (via `escape_string()` in controller) for all database inputs.
3. **Security First**: As this is a payment gateway, security is paramount. Never commit sensitive configuration files (`pp-config.php`).
4. **Documentation**: Update the relevant documentation if your changes introduce new constants or architectural shifts.

## Pull Request Process

1. Fork the repository and create your branch from `main`.
2. Ensure your code is tested in both root and subdirectory environments.
3. Submit a Pull Request with a clear description of the problem solved and the implementation details.

## Recent Updates: Dynamic Pathing
We have recently integrated a dynamic path detection system. Please use `PP_URL` for all resource linking instead of hardcoded strings or `$_SERVER['HTTP_HOST']` without path context.
