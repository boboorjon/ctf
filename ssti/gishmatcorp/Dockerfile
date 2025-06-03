FROM python:3.9-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install -r requirements.txt

COPY . .

RUN useradd -m -u 1000 ssti-user && chown -R ssti-user:ssti-user /app
USER ssti-user

EXPOSE 5000

CMD ["python", "app.py"]
