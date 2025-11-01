package com.example.attendance.service;

import com.example.attendance.model.Attendance;
import com.example.attendance.model.User;
import com.example.attendance.repo.AttendanceRepository;
import com.example.attendance.repo.UserRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.List;

@Service
public class AttendanceService {
    private final AttendanceRepository attendanceRepository;
    private final UserRepository userRepository;

    public AttendanceService(AttendanceRepository attendanceRepository, UserRepository userRepository) {
        this.attendanceRepository = attendanceRepository;
        this.userRepository = userRepository;
    }

    @Transactional
    public Attendance scanByNim(String nim) {
        User student = userRepository.findByNim(nim).orElseThrow(() -> new IllegalArgumentException("NIM not found"));
        LocalDate today = LocalDate.now();
        return attendanceRepository.findByStudentAndDate(student, today)
                .orElseGet(() -> {
                    Attendance a = new Attendance();
                    a.setStudent(student);
                    a.setDate(today);
                    a.setScannedAt(LocalDateTime.now());
                    return attendanceRepository.save(a);
                });
    }

    public List<Attendance> history(User student) {
        return attendanceRepository.findByStudentOrderByDateDesc(student);
    }
}
