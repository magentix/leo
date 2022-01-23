# Leo

Leo is the smallest Gemini server in the universe.

# Requirements

- PHP >= 7.4
- OpenSSL >= 1.1.1

# Configuration

Generate a self-signed certificate with **gemini** as passphrase:

```bash
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365
```

Start the server:

```bash
php leo.php &
```
