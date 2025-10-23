package com.example.presensi.repository;

import com.example.presensi.model.Attendance;
import com.example.presensi.model.User;
import org.springframework.data.jpa.repository.JpaRepository;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public interface AttendanceRepository extends JpaRepository<Attendance, Long> {
    List<Attendance> findByStudent(User student);
    Optional<Attendance> findByStudentAndDate(User student, LocalDate date);
}
