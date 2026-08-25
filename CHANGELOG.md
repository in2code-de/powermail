# Changelog

## 7.5.2 — 2026-08-25

### Security

- Backport of the mitigation for CVE-2026-77136 (Fluid server-side template injection in mail header values).
- Values submitted by a form visitor are no longer parsed as Fluid when Powermail prepares mail headers. This prevents submitted sender names or email addresses from executing Fluid ViewHelpers.
- Configured values continue to support Fluid as before:
  - receiver mail: receiver name and subject
  - sender and opt-in mail: sender name, sender email and subject

### Compatibility

- No database migration or form-field change is required. Keep `sender_name` markers and fields unchanged.
- A Fluid ViewHelper inside an `overwrite` value that replaces a visitor-controlled mail header is no longer evaluated. Plain-text values, including `receiver.overwrite.senderName`, continue to work unchanged.

