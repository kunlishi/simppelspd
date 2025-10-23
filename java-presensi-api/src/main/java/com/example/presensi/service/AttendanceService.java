package com.example.presensi.service;

import com.example.presensi.dto.AttendanceDtos;
import com.example.presensi.model.Attendance;
import com.example.presensi.model.User;
import com.example.presensi.repository.AttendanceRepository;
import com.example.presensi.repository.UserRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.time.LocalDate;
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
    public AttendanceDtos.AttendanceResponse scan(String nim, String officerIdentifier) {
        User student = userRepository.findByNim(nim).orElseThrow(() -> new IllegalArgumentException("Mahasiswa tidak ditemukan"));
        LocalDate today = LocalDate.now();
        Attendance attendance = attendanceRepository.findByStudentAndDate(student, today).orElseGet(Attendance::new);
        attendance.setStudent(student);
        attendance.setDate(today);
        attendance.setScannedAt(Instant.now());
        attendance.setScannedBy(officerIdentifier);
        Attendance saved = attendanceRepository.save(attendance);
        AttendanceDtos.AttendanceResponse resp = new AttendanceDtos.AttendanceResponse();
        resp.setId(saved.getId());
        resp.setNim(student.getNim());
        resp.setName(student.getName());
        resp.setDate(saved.getDate());
        resp.setScannedAt(saved.getScannedAt());
        return resp;
    }

    public List<Attendance> listForStudent(User student) {
        return attendanceRepository.findByStudent(student);
    }
}
