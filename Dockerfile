# docker build --rm --no-cache  -f files/docker/systemd/Dockerfile -t systemd /root/provision

FROM centos:7

MAINTAINER "Björn Dieding" <bjoern@xrow.de>

ENV container=docker

RUN yum -y swap -- remove systemd-container systemd-container-libs -- install systemd systemd-libs
RUN yum -y update; yum clean all
RUN (cd /lib/systemd/system/sysinit.target.wants/; for i in *; do [ $i == systemd-tmpfiles-setup.service ] || rm -f $i; done); \
rm -f /lib/systemd/system/multi-user.target.wants/*;\
rm -f /etc/systemd/system/*.wants/*;\
rm -f /lib/systemd/system/local-fs.target.wants/*; \
rm -f /lib/systemd/system/sockets.target.wants/*udev*; \
rm -f /lib/systemd/system/sockets.target.wants/*initctl*; \
rm -f /lib/systemd/system/basic.target.wants/*;\
rm -f /lib/systemd/system/anaconda.target.wants/*;
RUN yum -y install epel-release
RUN yum -y install patch git subversion python-pip redis ansible
RUN pip install redis
RUN yum -y install php

ADD env2host.service /etc/systemd/system/env2host.service
RUN chmod 755 /etc/systemd/system/env2host.service
ADD env2host.php /usr/local/bin/env2host.php
RUN chmod 755 /usr/local/bin/env2host.php
RUN systemctl enable env2host.service

VOLUME [ "/sys/fs/cgroup" ]

CMD ["/usr/sbin/init"]
