cmake_minimum_required(VERSION 3.20)
project(serverDataTransfer C)

## To change the build directory later on
# set(CMAKE_RUNTIME_OUTPUT_DIRECTORY "${CMAKE_CURRENT_SOURCE_DIR}/build/")

set(CMAKE_C_STANDARD 11)

add_executable(serverDataTransfer main.c)

find_package(PkgConfig REQUIRED)

# Add CURL libs to project
find_package(CURL REQUIRED)
include_directories(${CURL_INCLUDE_DIRS})
link_directories(${CURL_LIBRARY_DIRS})
add_definitions(${CURL_CFLAGS_OTHER})
target_link_libraries(serverDataTransfer ${CURL_LIBRARIES})

#Add winsock2 to project
target_link_libraries(serverDataTransfer wsock32 ws2_32)

# Add libYaml to project
pkg_check_modules(LibYaml REQUIRED yaml-0.1)
include_directories(${LibYaml_INCLUDE_DIRS})
link_directories(${LibYaml_LIBRARY_DIRS})
add_definitions(${LibYaml_CFLAGS_OTHER})
target_link_libraries(serverDataTransfer ${LibYaml_LIBRARIES})
