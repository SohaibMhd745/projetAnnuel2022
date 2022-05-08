cmake_minimum_required(VERSION 3.20)
project(serverDataTransfer C)

## To change the build directory later on
# set(CMAKE_RUNTIME_OUTPUT_DIRECTORY "${CMAKE_CURRENT_SOURCE_DIR}/build/")

set(CMAKE_C_STANDARD 11)

add_executable(serverDataTransfer main.c macros.h
        dataParsing/parseData.c dataParsing/parseData.h
        communications/commsSafety.c communications/commsSafety.h
        output/output.c output/output.h)

find_package(PkgConfig REQUIRED)

# Add CURL libs to project
find_package(CURL REQUIRED)
include_directories(${CURL_INCLUDE_DIRS})
link_directories(${CURL_LIBRARY_DIRS})
add_definitions(${CURL_CFLAGS_OTHER})
target_link_libraries(serverDataTransfer ${CURL_LIBRARIES})

# Add mysql to project
pkg_check_modules(mysql REQUIRED libmariadb)
include_directories(${mysql_INCLUDE_DIRS})
link_directories(${mysql_LIBRARY_DIRS})
add_definitions(${mysql_CFLAGS_OTHER})
target_link_libraries(serverDataTransfer ${mysql_LIBRARIES})
