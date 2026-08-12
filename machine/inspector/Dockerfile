FROM python:3.12-alpine
WORKDIR /machine
COPY machine/manifest.yml /machine/manifest.yml
COPY machine/inspector/inspect.py /machine/inspect.py
CMD ["python", "/machine/inspect.py"]
