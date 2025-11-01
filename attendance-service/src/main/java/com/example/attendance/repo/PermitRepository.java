package com.example.attendance.repo;

import com.example.attendance.model.Permit;
import com.example.attendance.model.PermitStatus;
import com.example.attendance.model.User;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface PermitRepository extends JpaRepository<Permit, Long> {
    List<Permit> findByStudentOrderByCreatedAtDesc(User student);
    List<Permit> findByStatusOrderByCreatedAtAsc(PermitStatus status);
}
