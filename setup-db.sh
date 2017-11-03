#
# This script re-creates the tables
#
# Only static tables are recreated, whereas the dynamic tables (customers, orders)
# are left untouched
#
# Notes:
#
#     * the script assumes the existence of "travel" script (see AGENT below)
#	which launches "mysql"; AGENT should be available on the PATH
#
#     * the script must only be run from the root of the web application
#

AGENT=travel
ERROR_MSG="This script must be run from the root of the web application"

if [ ! -d "sql" ]; then
	echo "${ERROR_MSG}" 1>&2
	exit 1
fi

cd sql || {
	echo "${ERROR_MSG}" 1>&2
	exit 1
}

${AGENT} <<EOF
	source start.sql;
EOF

