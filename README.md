# BOMS (Best Order Management System)

Welcome to BOMS, a dual portal system built with Laravel to make campus food ordering easy and organized for both vendors and customers. 

## What it does

BOMS handles the complete lifecycle of food orders. Vendors have a professional dashboard to manage incoming requests, while students can browse menus and checkout seamlessly. 

Key features include:
- Dual Portals: Separate login areas for vendors and customers.
- AI Magic Paste: Vendors can paste raw WhatsApp text and Gemini AI automatically extracts the customer name, items, and delivery info to create an order.
- Delivery Manifest: Orders are automatically grouped by time slot and campus block to make delivery runs super efficient.
- Predictive Prep List: Uses the last 4 weeks of order data to tell vendors exactly how much food they need to prepare for the next day.
- Live Dashboard: Vendor screens poll for new orders every 30 seconds and update automatically without a refresh.

## Tech Stack

- Backend: Laravel
- Frontend: Laravel Blade with custom CSS
- Database: MySQL
- AI Integration: Google Gemini API

## Setup

1. Clone the repository and run composer install
2. Copy .env.example to .env and set your database credentials
3. Add your GEMINI_API_KEY to the .env file
4. Run php artisan key:generate
5. Run php artisan migrate --seed to populate test accounts and 6 months of historical data
6. Start the server with php artisan serve

Test Accounts:
Vendor: vendor@campus.edu.my (password: password)
Customer: alif@campus.edu.my (password: password)
