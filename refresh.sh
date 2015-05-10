#
# This script re-creates the tables
#
# Only static tables are recreated, whereas the dynamic tables (customers, orders)
# are left untouched
#
# Notes:
#
#     * the script assumes the existence of "travel" script which launches "mysql",
#       and its availability in the path
#
#     * the script must only be run from the "sql" directory
#

AGENT=travel

CURRDIR=$(pwd)
CURRDIR=$(basename ${CURRDIR})

if [ "x${CURRDIR}" != "xsql" ]; then
	echo 'This script must be run from "sql" directory of the web program' 1>&2
	exit 1
fi

${AGENT} <<EOF
	source start.sql;
EOF

