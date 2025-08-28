# 🎤 Sermon Transcriber

**Transform your audio sermons into text with AI-powered transcription**

A modern Laravel application designed specifically for pastors, preachers, and religious leaders to transcribe their sermons using OpenAI's Whisper API. Features a beautiful Gen-Z aesthetic UI with glassmorphism effects and comprehensive functionality.

## ✨ Features

### 🎯 Core Functionality
- **Audio Transcription**: Upload audio files (MP3, WAV, M4A, MP4) up to 100MB
- **AI Summarization**: Generate intelligent summaries of sermons
- **Multi-language Support**: Auto-detect or specify English/Swahili
- **Export Options**: Download transcripts as PDF or Word documents
- **User Management**: Personal dashboard with user-specific transcripts

### 🎨 Modern UI/UX
- **Gen-Z Aesthetic**: Beautiful gradients and glassmorphism effects
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Drag & Drop**: Intuitive file upload with visual feedback
- **Real-time Feedback**: Loading states and progress indicators
- **Error Handling**: User-friendly error messages and validation

### 🔐 Security & Performance
- **Authentication**: Laravel Breeze with secure user management
- **File Validation**: Comprehensive file type and size validation
- **Secure Storage**: Unique file naming and proper storage management
- **Error Logging**: Detailed logging for debugging and monitoring

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM
- OpenAI API Key

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sermon-transcriber
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sermon_transcriber
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configure OpenAI API**
   ```env
   OPENAI_API_KEY=your_openai_api_key_here
   ```

6. **Run migrations and setup**
   ```bash
   php artisan migrate
   php artisan storage:link
   npm run build
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

## 📁 File Structure

```
sermon-transcriber/
├── app/
│   ├── Http/Controllers/
│   │   └── TranscriptionController.php    # Main transcription logic
│   └── Models/
│       └── Transcript.php                 # Transcript model
├── resources/views/
│   ├── transcribe/
│   │   ├── index.blade.php               # Main dashboard
│   │   └── pdf.blade.php                 # PDF export template
│   ├── auth/                             # Authentication views
│   └── layouts/                          # Layout templates
├── database/migrations/
│   └── create_transcripts_table.php      # Database schema
└── routes/
    └── web.php                           # Application routes
```

## 🔧 Configuration

### File Upload Limits
The application is configured to handle very large sermon files:

- **Maximum file size**: 300MB (supports sermons up to 2 hours)
- **Supported formats**: MP3, WAV, M4A, MP4
- **Timeout**: 30 minutes for transcription
- **Memory limit**: 1GB

### Server Requirements
For optimal performance with large files, ensure your server has:

```apache
# .htaccess configuration (already included)
php_value upload_max_filesize 300M
php_value post_max_size 300M
php_value max_execution_time 1800
php_value max_input_time 1800
php_value memory_limit 1024M
```

## 🎯 Usage

### For Pastors & Preachers

1. **Register/Login**: Create an account or sign in
2. **Upload Sermon**: Drag & drop or select your audio file
3. **Choose Language**: Select auto-detect or specify language
4. **Wait for Processing**: AI will transcribe your sermon
5. **Review & Export**: View transcript and download as needed
6. **Generate Summary**: Get AI-powered sermon summary

### Features for Religious Leaders

- **Very Long Sermon Support**: Handles sermons up to 300MB (2+ hours)
- **Mixed Language**: Perfect for Swahili/English sermons
- **Professional Export**: Clean PDF and Word formats
- **Personal Dashboard**: All your transcripts in one place
- **Secure Storage**: Your sermons are private and secure

## 🔐 Security Features

- **User Authentication**: Secure login/registration
- **File Validation**: Comprehensive security checks
- **CSRF Protection**: Built-in Laravel security
- **Input Sanitization**: All inputs are properly validated
- **Error Handling**: Secure error messages

## 🛠️ Development

### Adding New Features

1. **Create migration** for database changes
2. **Update model** with new relationships/attributes
3. **Modify controller** for new functionality
4. **Update views** for UI changes
5. **Test thoroughly** with real audio files

### Testing

```bash
# Run tests
php artisan test

# Check for errors
php artisan route:list
php artisan config:cache
```

## 📊 Performance Optimization

### For Large Files
- **Chunked uploads**: Files are processed in chunks
- **Background processing**: Long transcriptions don't block UI
- **Caching**: Results are cached for faster access
- **Cleanup**: Temporary files are automatically removed

### Monitoring
- **Logging**: All operations are logged
- **Error tracking**: Comprehensive error reporting
- **Performance metrics**: Track transcription times

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

### Common Issues

**File upload fails**
- Check file size (max 300MB)
- Verify file format (MP3, WAV, M4A, MP4)
- Ensure server has proper permissions

**Transcription fails**
- Verify OpenAI API key is valid
- Check internet connection
- Ensure audio file has clear speech

**Slow processing**
- Large files take longer (up to 30 minutes for 2-hour sermons)
- Check server resources
- Verify OpenAI API status

### Getting Help

1. Check the [Issues](https://github.com/Swai-D/audio-to-text-web-app/issues) page
2. Review the documentation
3. Contact support with detailed error information

## 🎉 Acknowledgments

- **OpenAI** for Whisper and GPT APIs
- **Laravel** for the amazing framework
- **Tailwind CSS** for beautiful styling
- **All contributors** who helped build this tool

---

**Built with ❤️ for pastors and religious leaders worldwide**
