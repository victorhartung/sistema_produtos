-- Relatório de Entrada de Estoque para todos os Produtos

SELECT
    p.name AS product_name,
    SUM(r.amount) AS total_amount,
    SUM(r.amount * p.cost_price) AS total_cost_price,
    SUM(r.amount * p.retail_price) AS total_retail_price
FROM
    requisitions r
JOIN
    products p ON r.product_id = p.id
WHERE
    r.is_exit = FALSE
    AND r.requisition_date BETWEEN '2023-11-10' AND '2024-12-10'
    AND p.composite = FALSE
GROUP BY
    p.id, p.name

UNION ALL

-- Relatório de Entrada de Estoque para Produtos Compostos

SELECT
    ps.name AS product_name,
    SUM(r.amount) AS total_amount,
    SUM(r.amount * ps.cost_price) AS total_cost_price,
    SUM(r.amount * ps.retail_price) AS total_retail_price
FROM
    requisitions r
JOIN
    composite_products cp ON r.product_id = cp.composite_id
JOIN
    products ps ON cp.composite_id = ps.id
WHERE
    r.is_exit = FALSE
    AND r.requisition_date BETWEEN '2023-11-10' AND '2024-12-10'
GROUP BY
    cp.simple_id, ps.name

ORDER BY
    product_name;

-- Relatório de saída do produto simples

SELECT
    p.id AS product_id,
    p.name AS product_name,
    SUM(r.amount) AS total_amount,
    SUM(r.amount * p.cost_price) AS total_cost_price,
    SUM(r.amount * p.retail_price) AS total_retail_price
FROM
    requisitions r
JOIN
    products p ON r.product_id = p.id
WHERE
    r.is_exit = TRUE
    AND r.requisition_date BETWEEN '2023-11-10' AND '2024-12-10'
    AND p.composite = FALSE
GROUP BY
    p.id, p.name

UNION ALL

-- Relatório de saída dos componentes do produto composto
SELECT
    ps.id AS product_id,
    ps.name AS product_name,
    SUM(r.amount * cp.amount) AS total_amount,
    SUM(r.amount * cp.amount * ps.cost_price) AS total_cost_price,
    SUM(r.amount * cp.amount * ps.retail_price) AS total_retail_price
FROM
    requisitions r
JOIN
    composite_products cp ON r.product_id = cp.composite_id
JOIN
    products ps ON cp.simple_id = ps.id
WHERE
    r.is_exit = TRUE
    AND r.requisition_date BETWEEN '2023-11-10' AND '2024-12-10'
GROUP BY
    ps.id, ps.name

ORDER BY
    product_name;


-- Relatório de requisição geral

SELECT
    r.id AS requisition_id,
    r.user_id,
    r.product_id,
    p.name AS product_name,
    r.amount,
    CASE 
        WHEN r.is_exit = TRUE THEN 'Saída'
        ELSE 'Entrada'
    END AS requisition_type,
    r.requisition_date,
    (r.amount * p.cost_price) AS total_cost_price,
    (r.amount * p.retail_price) AS total_retail_price
FROM
    requisitions r
JOIN
    products p ON r.product_id = p.id
ORDER BY
    r.requisition_date DESC, r.id;