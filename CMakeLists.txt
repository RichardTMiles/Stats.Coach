cmake_minimum_required(VERSION 3.5)
project(Stats_Coach)

set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11")

set(SOURCE_FILES
    ex.cpp)

add_executable(Stats_Coach ${SOURCE_FILES})