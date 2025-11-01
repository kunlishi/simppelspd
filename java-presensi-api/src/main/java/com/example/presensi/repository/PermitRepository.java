package com.example.presensi.repository;

import com.example.presensi.model.PermitRequest;
import com.example.presensi.model.PermitStatus;
import com.example.presensi.model.User;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface PermitRepository extends JpaRepository<PermitRequest, Long> {
    List<PermitRequest> findByStudent(User student);
    List<PermitRequest> findByStatus(PermitStatus status);
}
