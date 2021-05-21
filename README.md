# PGP Sign & Verify

- [Description](#description)
- [Todo](#todo)
- [ChangeLog](#changelog)
- [References](#references)
- [Notes](#notes)

## Description

PGP Sign & Verify is a Wordpress Plugin to sign a document using the OpenSSL Library.

```php
 * PGP Sign & Verify
 *
 * @package           PGPSignVerify
 * @author            Nicolas Georget
 * @copyright         2021 Integrity Asia
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       PGP Sign & Verify
 * Plugin URI:        https://www.integrity-asia.com
 * Description:       This plugin sign and verify a document using OpenSSL library.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nicolas Georget
 * Author URI:        https://keybase.io/ngeorget
 */
```

## Todo

- [ ] See Todo List in `~/pgpsign-install.php`
- [x] ~~*this is a complete item*~~ [2021-03-23]
- [ ] this is an incomplete item

## ChangeLog

## Notes

### Creating private & public keys

Use following command in command prompt to generate a keypair with a self-signed certificate.

```bash
openssl genrsa -out private.pem 2048 -nodes
```

Once you are successful with the above command a file (private.pem) will be created on your present directory, proceed to export the public key from the keypair generated. The command below shows how to do it.

```bash
openssl rsa -in private.pem -outform PEM -pubout -out public.pem
```


---

## References

[1] Table of contents generated with markdown-toc http://ecotrust-canada.github.io/markdown-toc/

