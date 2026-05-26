# CST5 Final Project: Inventory Management System

## System Architecture
- Pattern: Strict MVC (Model-View-Controller)
- Security: All DB queries MUST use secure prepared statements to prevent SQL Injection.
- Deployment Goal: Production-ready for cloud deployment.

## Guardrails
- NEVER hardcode credentials. Use process.env / environment variables.
- Ensure the app reads from environment variables seamlessly both locally and in production.
- Do not touch or expose the `.env` file.