# Background Jobs - Setup Guide

## Overview

The Kanban Task Manager uses a background job system for:
- Gmail email synchronization
- Google Calendar event synchronization
- Token refresh
- Due date reminders
- Cleanup tasks

## Components

### Job Queue System

**JobModel** (`app/Models/JobModel.php`)
- Database table: `jobs`
- Stores job types, payloads, status, and attempts
- Handles exponential backoff for failed jobs

**JobService** (`app/Services/JobService.php`)
- Dispatches jobs to the queue
- Runs pending jobs
- Handles job completion and failures
- Supports scheduled jobs

### Worker Command

```bash
# Run worker in one-shot mode
php spark jobs:work

# Run worker as daemon (continuous)
php spark jobs:work --daemon

# Adjust sleep interval between polls (default 5 seconds)
php spark jobs:work --daemon --sleep=10

# Limit number of jobs to process before exiting
php spark jobs:work --max-runs=100
```

## Available Commands

### Sync Commands

**Sync Gmail emails:**
```bash
# Sync for specific user
php spark sync:gmail --user-id=1

# Queue the sync job instead of running immediately
php spark sync:gmail --queue
```

**Sync Calendar events:**
```bash
# Sync specific calendar
php spark sync:calendar --user-id=1 --calendar-id=1

# Sync all enabled calendars
php spark sync:calendar --all

# Queue the sync job
php spark sync:calendar --all --queue
```

### Maintenance Commands

**Refresh Google tokens:**
```bash
php spark tokens:refresh
php spark tokens:refresh --queue
```

**Send due date reminders:**
```bash
php spark reminders:send --user-id=1
php spark reminders:send --queue
```

**Clean up old jobs:**
```bash
php spark jobs:cleanup --days=30
php spark jobs:cleanup --queue
```

**Renew expired Gmail watches:**
```bash
php spark gmail:renew-watches
```

## Production Deployment

### Using Supervisor

Supervisor keeps the worker running and automatically restarts it if it crashes.

1. Install Supervisor:
   ```bash
   sudo apt-get install supervisor
   ```

2. Copy the supervisor configuration:
   ```bash
   sudo cp docs/supervisor.conf /etc/supervisor/conf.d/kanban-worker.conf
   ```

3. Update the configuration with your project path:
   ```bash
   sudo nano /etc/supervisor/conf.d/kanban-worker.conf
   ```

4. Create log directory:
   ```bash
   sudo mkdir -p /var/log/supervisor
   sudo chown www-data:www-data /var/log/supervisor
   ```

5. Load the new configuration:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   ```

6. Start the worker:
   ```bash
   sudo supervisorctl start kanban-worker
   ```

7. Check status:
   ```bash
   sudo supervisorctl status kanban-worker
   ```

### Using Crontab

For periodic tasks that don't need a continuous worker.

1. Open crontab:
   ```bash
   crontab -e
   ```

2. Add entries from `docs/crontab.example` (adjust paths)

3. Create log directories:
   ```bash
   sudo mkdir -p /var/log/kanban
   sudo chown www-data:www-data /var/log/kanban
   ```

## Environment Configuration

Add to `.env`:

```env
# Google Cloud Pub/Sub (for Gmail webhooks)
gmail.pubsub.topic=projects/your-project/topics/your-topic

# Webhook secret for verification
gmail.webhook.secret=your-random-secret-string
```

## Setting Up Gmail Watch/Webhooks

### 1. Create Google Cloud Pub/Sub Topic

```bash
# Using gcloud CLI
gcloud pubsub topics create kanban-gmail-webhooks

# Create subscription
gcloud pubsub subscriptions create kanban-gmail-webhook-sub \
  --topic=kanban-gmail-webhooks \
  --push-endpoint=https://your-domain.com/gmail/webhook
```

### 2. Set webhook secret

Generate a random secret and add to `.env`:
```bash
openssl rand -base64 32
```

### 3. Enable Gmail Watch

Via API:
```bash
curl -X POST https://your-domain.com/gmail/watch/enable \
  -H "Cookie: your_session_cookie"
```

The watch will automatically renew before expiration (scheduled job).

## Monitoring

### Check job queue status:

```sql
-- View pending jobs
SELECT * FROM jobs WHERE status = 'pending' ORDER BY scheduled_at ASC;

-- View failed jobs
SELECT * FROM jobs WHERE status = 'failed' ORDER BY created_at DESC;

-- View running jobs
SELECT * FROM jobs WHERE status = 'running';

-- Clean up old completed jobs
DELETE FROM jobs WHERE status = 'completed' AND completed_at < NOW() - INTERVAL '30 days';
```

### Check supervisor logs:

```bash
# Tail worker logs
sudo tail -f /var/log/supervisor/kanban-worker.log

# Check supervisor status
sudo supervisorctl status
```

### Check cron logs:

```bash
# View sync logs
tail -f /var/log/kanban/gmail-sync.log
tail -f /var/log/kanban/calendar-sync.log

# View error logs
tail -f /var/log/kanban/*.log
```

## Troubleshooting

### Worker not processing jobs

1. Check if worker is running:
   ```bash
   sudo supervisorctl status kanban-worker
   ```

2. Check worker logs:
   ```bash
   sudo tail -100 /var/log/supervisor/kanban-worker.log
   ```

3. Verify jobs exist in queue:
   ```sql
   SELECT COUNT(*) FROM jobs WHERE status = 'pending';
   ```

### Gmail watch expiring

1. Check watch expiration:
   ```sql
   SELECT * FROM gmail_watches WHERE is_active = true;
   ```

2. Manually renew:
   ```bash
   php spark gmail:renew-watches
   ```

3. Verify Pub/Sub topic is configured in `.env`

### Token refresh failing

1. Check tokens table:
   ```sql
   SELECT * FROM google_tokens;
   ```

2. Verify `.env` has correct Google client credentials

3. Manually refresh:
   ```bash
   php spark tokens:refresh
   ```