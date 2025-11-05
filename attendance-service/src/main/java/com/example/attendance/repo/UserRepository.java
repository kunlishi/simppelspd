package com.example.attendance.repo;

import com.example.attendance.model.User;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.Optional;

public interface UserRepository extends JpaRepository<User, Long> {
    Optional<User> findByUsername(String username);
    Optional<User> findByNim(String nim);
    boolean existsByUsername(String username);
    boolean existsByNim(String nim);
}
