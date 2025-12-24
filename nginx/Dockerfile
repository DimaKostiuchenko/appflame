FROM nginx:alpine

# Since context is now ".", we path from the root of the project
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# This will now work because ./backend is inside the context
COPY ./backend/public /var/www/public