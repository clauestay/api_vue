FROM node:lts-alpine

RUN npm install -g http-server

RUN mkdir /app

WORKDIR /app

# Copiar archivos de configuración de npm y .env
COPY ./package*.json ./
COPY ./.env ./

# Instalar dependencias
RUN npm install

# Copiar el código fuente
COPY . .

# Construir la aplicación para producción
#RUN npm run build
# RUN npm run dev

# Exponer el puerto 80
EXPOSE 8080

# CMD ["http-server", "dist"]
# CMD ["npm", "run", "dev"]
CMD ["tail", "-f", "/dev/null"]