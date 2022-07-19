# Changelog

---

## 2.0.0.0 (2022-07-19)

- adjust to PHP >= 7.3 and current dependency packages

---

## 1.3.0.0 (2022-07-18)

- tests use generated example phone numbers
- move recipient checks from list to recipient itself
- tests added

---

## 1.2.1.0 (2022-07-15)

- extend log messages
- sanitize special phone number format before request

---

## 1.2.0.0 (2022-07-14)

- make sender number optional
- assign sender address type only if sender is set
- collect exception messages in a class
- collect URI parts in a class
- extract logger handler from client

---

## 1.1.0.0 (2022-07-13)

- make installable in PHP 8
- remove unused dependency

---

## 1.0.0.0 (2022-07-13)

- initial implementation
  - SMS requests (text or binary)
  - SMS responses
  - recipient managing