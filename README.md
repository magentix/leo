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

Start the server:

```bash
php leo.php &
```
