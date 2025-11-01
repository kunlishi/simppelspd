package com.example.attendance.config;

import com.example.attendance.model.Role;
import com.example.attendance.model.User;
import com.example.attendance.repo.UserRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.password.PasswordEncoder;

@Configuration
public class DataInitializer {

    @Bean
    CommandLineRunner initUsers(UserRepository userRepository, PasswordEncoder passwordEncoder) {
        return args -> {
            if (userRepository.findByUsername("admin").isEmpty()) {
                User admin = new User();
                admin.setUsername("admin");
                admin.setPassword(passwordEncoder.encode("admin123"));
                admin.setFullName("Administrator");
                admin.setEmail("admin@example.com");
                admin.setRole(Role.ADMIN);
                userRepository.save(admin);
            }
            if (userRepository.findByUsername("petugas").isEmpty()) {
                User p = new User();
                p.setUsername("petugas");
                p.setPassword(passwordEncoder.encode("petugas123"));
                p.setFullName("Petugas Apel");
                p.setRole(Role.PETUGAS);
                userRepository.save(p);
            }
            if (userRepository.findByUsername("23000001").isEmpty()) {
                User s = new User();
                s.setUsername("23000001");
                s.setPassword(passwordEncoder.encode("student123"));
                s.setNim("23000001");
                s.setFullName("Mahasiswa Satu");
                s.setKelas("IF-1");
                s.setEmail("mhs1@example.com");
                s.setRole(Role.STUDENT);
                userRepository.save(s);
            }
        };
    }
}
