<?php
echo "<script>console.log('admin ok');</script>";


function getUsersCount($pdo, $type){
    if ($type === 'client') {
        // Count only clients
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients");
        $stmt->execute();
        return (int) $stmt->fetchColumn() -1 ;

    } elseif ($type === 'agency') {
        // Count only agencies
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agency"); // Use your actual agency table name
        $stmt->execute();
        return (int) $stmt->fetchColumn() - 1;

    } elseif ($type === 'all') {
        // Count clients
        $stmtClients = $pdo->prepare("SELECT COUNT(*) FROM clients");
        $stmtClients->execute();
        $clientCount = (int) $stmtClients->fetchColumn();

        // Count agencies
        $stmtAgencies = $pdo->prepare("SELECT COUNT(*) FROM agency"); // Use your actual agency table name
        $stmtAgencies->execute();
        $agencyCount = (int) $stmtAgencies->fetchColumn();

        // Return the sum
        return $clientCount + $agencyCount - 2;

    }
}
function getClients($pdo){
    echo "<script>console.log('get client have been called')</script>";
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE fullName != '' ");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<script>console.log('get client have been called')</script>";

    return $clients;
}
function getAgencys($pdo){
    echo "<script>console.log('get agency have been called')</script>";
    $stmt = $pdo->prepare("SELECT * FROM agency WHERE fullName != ''");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<script>console.log('get agency have been called')</script>";

    return $clients;
}

function deleteMessage($pdo, $id){
    $stmt = $pdo->prepare("DELETE FROM message WHERE id = ?");
    $stmt->execute([$id]);
}

function deleteClient($pdo, $id){
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}

function deleteAgency($pdo, $id){
    $stmt = $pdo->prepare("DELETE FROM agency WHERE id = ?");
    $stmt->execute([$id]);
}

?>