FROM python:3.12-slim
WORKDIR /opt/harmonicdb
COPY systems/harmonicdb/pyproject.toml ./
COPY systems/harmonicdb/harmonic ./harmonic
RUN pip install --no-cache-dir .
COPY systems/porter /opt/porter
RUN pip install --no-cache-dir /opt/porter
COPY machine/porter/harmonic_host.py /usr/local/bin/harmonic-host
COPY machine/porter/harmonic_adapter.py /usr/local/bin/harmonic-adapter
RUN chmod +x /usr/local/bin/harmonic-host /usr/local/bin/harmonic-adapter && useradd --create-home --uid 10001 harmonic && mkdir -p /data /porter && chown -R harmonic:harmonic /data /porter
USER harmonic
CMD ["harmonic-host"]
