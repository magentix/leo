# Leo

Leo is the smallest Gemini server in the universe.

# Requirements

- PHP >= 8.0
- OpenSSL >= 1.1.1

# Configuration

Generate a self-signed certificate with **gemini** as passphrase and your server FQDN as common name:

```bash
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365
```

```
Enter PEM pass phrase: gemini
Verifying - Enter PEM pass phrase: gemini
Country Name (2 letter code) [AU]: US
State or Province Name (full name) [Some-State]: Washington
Locality Name (eg, city) []: Olympia
Organization Name (eg, company) [Internet Widgits Pty Ltd]: MyCompany
Organizational Unit Name (eg, section) []: IT
Common Name (e.g. server FQDN or YOUR name) []: example.com
Email Address []: hello@example.com
```

Start the server:

```bash
php leo.php &
```
