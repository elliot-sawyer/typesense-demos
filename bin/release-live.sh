#!/bin/bash

vendor/bin/sake dev/tasks/migrations
vendor/bin/sake-release
vendor/bin/sake dev/tasks/migrations
vendor/bin/sake dev/tasks/Firesphere-CSPHeaders-Tasks-SRIRefreshTask
