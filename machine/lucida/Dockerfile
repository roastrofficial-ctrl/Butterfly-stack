FROM python:3.12-slim
WORKDIR /lucida
COPY systems/lucida/pyproject.toml ./
COPY systems/lucida/lucida ./lucida
COPY systems/lucida/lab ./lab
COPY systems/lucida/lab-data ./lab-data
RUN pip install --no-cache-dir .
ENV LUCIDA_HOST=0.0.0.0
EXPOSE 8077
CMD ["lucida", "lab", "--port", "8077", "--no-browser"]
