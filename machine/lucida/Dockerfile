FROM python:3.12-slim
WORKDIR /lucida
COPY systems/lucida/pyproject.toml ./
COPY systems/lucida/lucida ./lucida
COPY systems/lucida/lab ./lab
COPY systems/lucida/lab-data ./lab-data
COPY machine/lucida/sights/find-me-network.jpg /source/find-me-network.jpg
RUN pip install --no-cache-dir .
ENV LUCIDA_HOST=0.0.0.0 LUCIDA_HERO_INPUT=/source/find-me-network.jpg
EXPOSE 8077
CMD ["lucida", "lab", "--port", "8077", "--no-browser"]
