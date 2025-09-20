
# Zhyvytsia WordPress Theme

## Overview

Zhyvytsia is a custom-built, high-performance WordPress theme developed for the e-commerce website [zhyvytsia.com.ua](https://zhyvytsia.com.ua/). It is a modern, developer-oriented theme that leverages a range of contemporary tools and technologies to deliver a fast, scalable, and maintainable solution. The theme is built with a strong focus on custom functionality, including e-commerce features, payment gateway integrations, and a custom Gutenberg block library.

## Key Technologies

*   **Templating:** The theme uses [Timber](https://timber.github.io/docs/) and the [Twig](https://twig.symfony.com/) templating engine, which separates HTML from PHP logic, resulting in cleaner, more maintainable code.
*   **Backend:** The theme is built on a robust PHP 7.4 foundation and utilizes [Composer](https://getcomposer.org/) for managing server-side dependencies.
*   **Frontend:** The theme uses [Sass](https://sass-lang.com/) for advanced CSS pre-processing and is structured to support modern JavaScript development. While a specific framework is not enforced, the theme is flexible enough to integrate with libraries like React or Vue.js.
*   **Custom Gutenberg Blocks:** The theme features a bespoke set of Gutenberg blocks, allowing for flexible and intuitive content management within the WordPress editor.
*   **E-commerce:** The theme includes custom e-commerce functionality, with integrations for multiple payment gateways and shipping services.

## Features

*   **Custom E-commerce Platform:** The theme bypasses traditional e-commerce plugins like WooCommerce in favor of a lightweight, custom-built solution tailored to the specific needs of the Zhyvytsia store.
*   **Payment Gateway Integrations:** The theme includes seamless integrations with popular Ukrainian payment providers, including:
    *   Monobank
    *   LiqPay
*   **Shipping Provider Integration:** The theme integrates with the Nova Poshta API for real-time shipping calculations and order management.
*   **Geolocation Services:** The theme uses the GeoIP2 library to provide location-based features and enhance the user experience.
*   **Performance Optimization:** The theme is designed for speed, with features like HTML caching, disabled WordPress embeds, and optimized image loading.
*   **Developer-Friendly:** The theme is built with developers in mind, featuring a clean and organized codebase, a modular structure, and a range of helper functions and constants.
*   **Custom Admin Options:** The theme includes a custom options panel for managing theme settings, API keys, and other configuration options.
*   **Multilingual Ready:** The theme is designed to be fully compatible with WPML for multilingual support.

## Dependencies

### PHP (via Composer)

*   `plakidan/monobank-pay`: For Monobank payment gateway integration.
*   `geoip2/geoip2`: For geolocation services.
*   `lis-dev/nova-poshta-api-2`: For Nova Poshta shipping integration.
*   `liqpay/liqpay`: For LiqPay payment gateway integration.

### Frontend

The theme uses `prepros` for asset compilation. Refer to `prepros.config` for the build process. Key frontend technologies include:

*   **Sass:** For CSS pre-processing.
*   **jQuery:** For legacy JavaScript functionality.
*   **Custom JavaScript modules:** For interactive elements and AJAX-powered features.

## Theme Structure

The theme follows a logical and organized file structure:

*   `assets/`: Contains all frontend assets, including CSS, JavaScript, images, and fonts.
*   `core/`: The heart of the theme, containing all core logic, custom post types, taxonomies, Gutenberg blocks, and third-party integrations.
*   `vendor/`: Contains all Composer dependencies.
*   `views/`: Contains all Twig templates, following the Timber templating structure.

## Getting Started

To use this theme, you will need to have WordPress installed and running on a server with PHP 7.4 or higher.

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    ```
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Install frontend dependencies and compile assets:**
    This theme uses Prepros for asset compilation. Open the project in Prepros to compile the Sass and JavaScript files.
4.  **Activate the theme:**
    In the WordPress admin panel, go to **Appearance > Themes** and activate the "Zhyvytsia" theme.

## Configuration

After activating the theme, you will need to configure the following:

*   **API Keys:** Add your API keys for Monobank, LiqPay, and Nova Poshta in the theme options panel.
*   **General Settings:** Configure your site logo, contact information, and other general settings in the theme options panel.
*   **Redirects:** Set up any necessary 301 redirects in the theme options panel.

## Author

*   **Vitalii Kaplia** - [vitaliikaplia.com](https://vitaliikaplia.com/)

---
*This README was generated based on an analysis of the theme's source code.*
