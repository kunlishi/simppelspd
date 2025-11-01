package com.example.attendance.repo;

import com.example.attendance.model.Attendance;
import com.example.attendance.model.User;
import org.springframework.data.jpa.repository.JpaRepository;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public interface AttendanceRepository extends JpaRepository<Attendance, Long> {
    Optional<Attendance> findByStudentAndDate(User student, LocalDate date);
    List<Attendance> findByStudentOrderByDateDesc(User student);
}
