-- Create database
CREATE DATABASE IF NOT EXISTS GIKONKO_TSS;
USE GIKONKO_TSS;

-- Trades table
CREATE TABLE Trades (
    Trade_Id INT AUTO_INCREMENT PRIMARY KEY,
    Trade_Name VARCHAR(100) NOT NULL UNIQUE
);

-- Modules table
CREATE TABLE Modules (
    Module_Id INT AUTO_INCREMENT PRIMARY KEY,
    Module_Name VARCHAR(100) NOT NULL,
    Trade_Id INT NOT NULL,
    FOREIGN KEY (Trade_Id) REFERENCES Trades(Trade_Id) ON DELETE CASCADE
);

-- Trainees table
CREATE TABLE Trainees (
    Trainee_Id INT AUTO_INCREMENT PRIMARY KEY,
    FirstNames VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Gender ENUM('Male', 'Female', 'Other') NOT NULL,
    Trade_Id INT NOT NULL,
    FOREIGN KEY (Trade_Id) REFERENCES Trades(Trade_Id) ON DELETE CASCADE
);

-- Marks table
CREATE TABLE Marks (
    Mark_Id INT AUTO_INCREMENT PRIMARY KEY,
    Trainee_Id INT NOT NULL,
    Module_Id INT NOT NULL,
    Formative_Assessment DECIMAL(5,2) CHECK (Formative_Assessment BETWEEN 0 AND 50),
    Summative_Assessment DECIMAL(5,2) CHECK (Summative_Assessment BETWEEN 0 AND 50),
    Total_Marks DECIMAL(5,2) AS (Formative_Assessment + Summative_Assessment) STORED,
    FOREIGN KEY (Trainee_Id) REFERENCES Trainees(Trainee_Id) ON DELETE CASCADE,
    FOREIGN KEY (Module_Id) REFERENCES Modules(Module_Id) ON DELETE CASCADE,
    UNIQUE (Trainee_Id, Module_Id)
);

-- Users table
CREATE TABLE Users (
    User_Id INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Admin', 'DOS', 'Teacher') NOT NULL
);

-- Insert some sample Trades
INSERT INTO Trades (Trade_Name) VALUES
('ICT & Multimedia'),
('Building Construction'),
('Electrical Technolog
