# Student Profiling Render + Aiven Deployment Fix
Status: In Progress

## Steps:
- [x] 1. Create TODO.md
- [x] 2. Fix config/database.php (env vars, default=mysql)
- [x] 3. Update render.yml (remove unused PG DB)
- [x] 4. Update start.sh (safe APP_KEY gen + migrate)
- [x] 5. Update Dockerfile (download Aiven CA)
- [ ] 6. User: Set Render Environment Variables
- [ ] 7. User: Download Aiven CA.pem, set MYSQL_ATTR_SSL_CA
- [ ] 8. Git push, deploy, check logs
- [ ] 9. Run php artisan migrate on Aiven if needed
- [ ] 10. Test app

## Render ENV Vars needed:
```
APP_NAME=StudentProfiling
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=mysql-27361989-lourdaaanallen-c3df.h.aivencloud.com
DB_PORT=21975  # check Aiven console!
DB_DATABASE=your_db_name
DB_USERNAME=your_user
DB_PASSWORD=your_pass

MYSQL_ATTR_SSL_CA='-----BEGIN CERTIFICATE-----...-----END CERTIFICATE-----'  # paste ca.pem content
```

