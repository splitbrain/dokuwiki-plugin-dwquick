CREATE TABLE urls (handle, url, PRIMARY KEY (handle));
CREATE UNIQUE INDEX idx_url_handle ON urls(handle);

CREATE TABLE statistics (handle, calls, PRIMARY KEY(handle));
CREATE UNIQUE INDEX idx_statistics_handle ON statistics(handle);