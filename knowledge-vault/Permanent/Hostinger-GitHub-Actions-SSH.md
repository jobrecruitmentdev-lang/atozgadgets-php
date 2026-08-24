---
title: Hostinger GitHub Actions SSH
tags: [deploy, infra, ssh]
updated: 2026-08-13
---

# Hostinger GitHub Actions SSH

**Purpose:** Document the correct way to use SSH keys in GitHub Actions for Hostinger deployments.

**Summary:** Hostinger SSH can be strict. Third-party actions like `appleboy/ssh-action` or `webfactory/ssh-agent` can fail or cause silent formatting bugs when keys are pasted from Windows. Using a raw file with explicit identity and `StrictHostKeyChecking=no` bypasses formatting and known_hosts errors.

## Content
When deploying via Rsync from GitHub Actions to Hostinger, do not use `ssh-agent`. Instead, explicitly write the private key to a file using a heredoc and `cat`.

### Setup
```yaml
- name: Setup SSH Key
  run: |
    mkdir -p ~/.ssh
    cat << 'EOF' > ~/.ssh/id_ed25519
    ${{ secrets.HOSTINGER_SSH_KEY }}
    EOF
    chmod 600 ~/.ssh/id_ed25519
```

### Usage
Force `rsync` and `ssh` to use exactly this key and ignore Hostinger's host keys (since they rotate or aren't easily added to `known_hosts` dynamically).

```bash
rsync -e "ssh -p 65002 -i ~/.ssh/id_ed25519 -o StrictHostKeyChecking=no -o IdentitiesOnly=yes" ...
```

**Common Mistakes:**
1. Using the `Public Key` instead of the `Private Key` in GitHub Secrets. The secret must start with `-----BEGIN OPENSSH PRIVATE KEY-----`.
2. Extra indentation/spaces at the start of the base64 string in the secret.
3. Using `ssh-keyscan` which crashes if the directory doesn't exist, and is redundant when using `StrictHostKeyChecking=no`.

## Related
- [[DevOps-MOC]]
- Repo: `../../.github/workflows/deploy.yml`

## References
- None
