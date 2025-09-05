# Setup Guide - Sermon Transcriber

## Quick Start

1. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

2. **Database Setup**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

3. **Environment Configuration**
   
   Copy `.env.example` to `.env` and update these key settings:
   
   ```env
   APP_NAME="Sermon Transcriber"
   APP_URL=http://localhost:8000
   FILESYSTEM_DISK=public
   
   # Database (SQLite for development)
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   
   # OpenAI API Key (REQUIRED)
   OPENAI_API_KEY=sk-your-openai-api-key-here
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run the Application**
   ```bash
   php artisan serve
   ```

## Required Environment Variables

### Essential
- `OPENAI_API_KEY` - Your OpenAI API key for Whisper and GPT services

### Database (choose one)
- **SQLite** (recommended for development):
  ```env
  DB_CONNECTION=sqlite
  DB_DATABASE=/absolute/path/to/database.sqlite
  ```

- **MySQL**:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=sermon_transcriber
  DB_USERNAME=root
  DB_PASSWORD=your_password
  ```

- **PostgreSQL**:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=sermon_transcriber
  DB_USERNAME=postgres
  DB_PASSWORD=your_password
  ```

### File Storage
```env
FILESYSTEM_DISK=public
```

## Testing the Setup

1. Visit `http://localhost:8000`
2. Upload a small audio file (mp3, wav, m4a)
3. Select language (auto-detect recommended)
4. Click "Transcribe"
5. Wait for OpenAI Whisper to process
6. View the transcribed text
7. Try the summarize feature
8. Test PDF/Word export

## Troubleshooting

### Common Issues

1. **"Database does not exist"**
   - For SQLite: Create the database file manually or use absolute path
   - For MySQL/PostgreSQL: Create the database first

2. **"Storage link already exists"**
   - This is normal, the link is already created

3. **"OpenAI API error"**
   - Check your API key is correct
   - Verify you have credits in your OpenAI account
   - Check network connectivity

4. **"File upload fails"**
   - Ensure `storage/app/public` is writable
   - Check PHP upload limits in `php.ini`

### File Permissions (Linux/Mac)

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### PHP Extensions Required

- `ext-gd` (for PDF generation)
- `ext-zip` (for Word export)
- `ext-fileinfo` (for file uploads)

## Production Deployment

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Use a production database (MySQL/PostgreSQL)
4. Configure proper file permissions
5. Set up SSL certificate
6. Configure web server (Apache/Nginx)

## API Usage

The app uses these OpenAI endpoints:
- **Whisper API**: `https://api.openai.com/v1/audio/transcriptions`
- **Chat Completions**: `https://api.openai.com/v1/chat/completions`

Estimated costs:
- Whisper: ~$0.006 per minute of audio
- GPT-4o-mini: ~$0.00015 per 1K tokens for summaries
