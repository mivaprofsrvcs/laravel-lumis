# Security Policy

## Supported Versions

We provide security fixes for the following versions of this project.

| Version | Supported |
| ------- | --------- |
| Latest major release | ✅ |
| Previous major release | ✅ (security fixes only) |
| Older major releases | ❌ |

Notes:
- "Latest major release" refers to the most recent stable major version available via Composer.
- Security fixes may be released as patch versions (for example, `x.y.Z`) and may be backported to the previous major release when feasible.

## Reporting a Vulnerability

If you believe you have found a security vulnerability, please report it privately.

### Preferred method: GitHub Private Vulnerability Reporting
Use the repository's GitHub Security page to submit a report:
- Go to the repository's **Security** tab
- Click **Report a vulnerability**

This method is preferred because it supports coordinated disclosure and secure communication.

### Alternate method: Security email
If you cannot use GitHub private reporting, email us at:

`mivaprofsrvcs3@gmail.com`

Please include "SECURITY" in the subject line.

### What to include in your report
To help us validate and triage quickly, please include:

- A clear description of the issue and the security impact
- Affected versions and configuration details
- Reproduction steps or a proof-of-concept (PoC)
- Any known mitigations or workarounds
- If applicable, relevant logs, stack traces, request/response samples, or screenshots
- Your suggested severity (Low/Medium/High/Critical) and rationale

If the issue is in a dependency, please also reference the upstream advisory if known.

## Response and Disclosure Policy

### Acknowledgement and triage
- We aim to acknowledge reports within **2 business days**.
- We aim to provide an initial triage assessment within **5 business days**.

### Remediation targets (best effort)
We prioritize fixes based on severity and exploitability:

- **Critical**: target fix or mitigation within **7 days**
- **High**: target fix or mitigation within **14 days**
- **Medium**: target fix or mitigation within **30 days**
- **Low**: handled as capacity allows, typically within a regular release cadence

These are targets, not guarantees. If timelines change, we will communicate status updates to the reporter.

### Coordinated disclosure
We request that you do not publicly disclose the vulnerability until:
- A fix is released, or
- We mutually agree on a disclosure timeline

We typically coordinate disclosure once a patch is available and users have a reasonable opportunity to update.

### Public advisories
When appropriate, we will publish a GitHub Security Advisory and include:
- Affected versions
- Severity and CVSS (when applicable)
- Mitigations/workarounds
- Fixed versions
- Upgrade guidance

## Safe Harbor (Good-Faith Security Research)

We support responsible disclosure and will not pursue legal action against individuals who:
- Make a good-faith effort to avoid privacy violations, data destruction, and service disruption
- Only test against accounts and data they own or have explicit permission to use
- Do not exfiltrate, retain, or disclose sensitive data beyond what is necessary to demonstrate the issue
- Do not use social engineering, phishing, or physical attacks against our staff or infrastructure
- Provide us a reasonable time to remediate before public disclosure

If you are unsure whether your testing is authorized or safe, contact us first via the reporting channels above.

## Security Best Practices for Users

If you are deploying or consuming this package:

- Keep dependencies up to date (Composer + lock file hygiene).
- Use the principle of least privilege for API keys, database users, and service accounts.
- Do not commit secrets to version control; use environment variables and a secrets manager.
- Review framework and platform security guidance (Laravel, PHP, web server configuration).
- Enable HTTPS and secure cookie settings where applicable.

## Scope

This security policy applies to:
- This repository's code and its directly maintained components

This policy does not cover:
- Vulnerabilities in third-party dependencies (please report upstream, and optionally notify us)
- Issues that only affect outdated or unsupported versions
- Social engineering, phishing, or physical security issues

## Credits

We appreciate responsible security researchers and will acknowledge contributions in advisories where appropriate, unless anonymity is requested.
