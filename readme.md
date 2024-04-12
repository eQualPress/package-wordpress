# eQual WordPress Integration

## Description

This package provides integration between WordPress and the eQual framework. It handles user authentication,
registration, profile updates, and logout processes, synchronizing user data between the two platforms.

## Features

- **User Authentication:** Users can log in to WordPress using their eQual credentials.
- **User Registration:** New user registrations are automatically synchronized with eQual.
- **Profile Updates:** User profile updates in WordPress are reflected in eQual.
- **Logout Process:** User logout processes are handled in both WordPress and eQual.

## Installation

1. Upload the `wordpress` package directory to the `/packages` directory.
2. Activate the package:
    ```bash
    equal.run --do=init_package --package=wordpress
    ```
