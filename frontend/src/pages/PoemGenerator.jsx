import React, { useState } from 'react';
import { 
  Box, 
  TextField, 
  Button, 
  Typography, 
  Paper, 
  CircularProgress, 
  Alert, 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Grid,
  Chip,
  IconButton,
  Card,
  CardContent
} from '@mui/material';
import CloseIcon from '@mui/icons-material/Close';
import ContentCopyIcon from '@mui/icons-material/ContentCopy';
import SaveIcon from '@mui/icons-material/Save';
import axios from 'axios';

const PoemGenerator = () => {
  const [prompt, setPrompt] = useState('');
  const [poemType, setPoemType] = useState('classical');
  const [keywords, setKeywords] = useState([]);
  const [currentKeyword, setCurrentKeyword] = useState('');
  const [generatedPoem, setGeneratedPoem] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);
  
  const handleKeywordAdd = () => {
    if (currentKeyword.trim() && !keywords.includes(currentKeyword.trim())) {
      setKeywords([...keywords, currentKeyword.trim()]);
      setCurrentKeyword('');
    }
  };
  
  const handleKeywordDelete = (keywordToDelete) => {
    setKeywords(keywords.filter(keyword => keyword !== keywordToDelete));
  };
  
  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleKeywordAdd();
    }
  };
    const handleGeneratePoem = async () => {
    setLoading(true);
    setError(null);
    setSuccess(null);
    setGeneratedPoem(null);
    
    if (!prompt.trim()) {
      setError('يرجى إدخال موضوع للقصيدة');
      setLoading(false);
      return;
    }
    
    try {
      const response = await axios.post('/api/generate-poem', {
        prompt: prompt.trim(),
        poem_type: poemType,
        keywords: keywords.length > 0 ? keywords : undefined,
        max_length: 1500
      });
      
      if (response.data.success) {
        setGeneratedPoem({
          title: response.data.title,
          text: response.data.poem,
          model: response.data.model || 'unknown'
        });
        setSuccess('تم إنشاء القصيدة بنجاح!');
      } else {
        setError(response.data.message || 'فشل إنشاء القصيدة');
      }
    } catch (err) {
      console.error('Error generating poem:', err);
      const errorMessage = err.response?.data?.error || err.response?.data?.message || 'حدث خطأ أثناء إنشاء القصيدة.';
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  };
  
  const handleCopyPoem = () => {
    if (generatedPoem) {
      const poemText = `${generatedPoem.title}\n\n${generatedPoem.text}`;
      navigator.clipboard.writeText(poemText)
        .then(() => {
          setSuccess('تم نسخ القصيدة إلى الحافظة');
          setTimeout(() => setSuccess(null), 3000);
        })
        .catch(err => {
          console.error('Failed to copy poem:', err);
          setError('فشل نسخ القصيدة');
        });
    }
  };
  
  const handleSavePoem = async () => {
    if (!generatedPoem) return;
    
    setLoading(true);
    setError(null);
    setSuccess(null);
    
    try {
      const response = await axios.post('/api/poems', {
        title: generatedPoem.title,
        poem_text: generatedPoem.text,
        poem_type: poemType,
        keywords,
        is_public: true,
        generated_with_model: generatedPoem.model
      });
      
      if (response.status === 201) {
        setSuccess('تم حفظ القصيدة بنجاح!');
      }
    } catch (err) {
      console.error('Error saving poem:', err);
      setError('حدث خطأ أثناء حفظ القصيدة.');
    } finally {
      setLoading(false);
    }
  };
  
  return (
    <Box sx={{ p: 2, maxWidth: '1000px', margin: '0 auto' }}>
      <Typography variant="h4" component="h1" gutterBottom textAlign="center">
        منشئ القصائد العربية
      </Typography>
      
      <Paper elevation={3} sx={{ p: 3, mb: 4 }}>
        {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}
        {success && <Alert severity="success" sx={{ mb: 2 }}>{success}</Alert>}
        
        <Grid container spacing={3}>
          <Grid item xs={12}>
            <TextField
              label="موضوع القصيدة"
              placeholder="اكتب موضوع القصيدة هنا، مثال: الوطن، الصحراء، الحب..."
              value={prompt}
              onChange={(e) => setPrompt(e.target.value)}
              fullWidth
              multiline
              rows={2}
            />
          </Grid>
          
          <Grid item xs={12} md={6}>
            <FormControl fullWidth>
              <InputLabel>نوع القصيدة</InputLabel>
              <Select
                value={poemType}
                label="نوع القصيدة"
                onChange={(e) => setPoemType(e.target.value)}
              >
                <MenuItem value="classical">فصحى (عمودية)</MenuItem>
                <MenuItem value="nabati">نبطية (شعبية)</MenuItem>
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={12} md={6}>
            <TextField
              label="الكلمات المفتاحية"
              placeholder="أضف كلمات مفتاحية ثم اضغط Enter"
              value={currentKeyword}
              onChange={(e) => setCurrentKeyword(e.target.value)}
              onKeyPress={handleKeyPress}
              fullWidth
              InputProps={{
                endAdornment: (
                  <Button 
                    onClick={handleKeywordAdd}
                    disabled={!currentKeyword.trim()}
                    variant="contained" 
                    color="primary" 
                    size="small"
                    sx={{ ml: 1 }}
                  >
                    إضافة
                  </Button>
                ),
              }}
            />
            
            <Box sx={{ mt: 1, display: 'flex', flexWrap: 'wrap', gap: 1 }}>
              {keywords.map((keyword, index) => (
                <Chip
                  key={index}
                  label={keyword}
                  onDelete={() => handleKeywordDelete(keyword)}
                  color="primary"
                  variant="outlined"
                />
              ))}
            </Box>
          </Grid>
          
          <Grid item xs={12} sx={{ textAlign: 'center' }}>
            <Button
              variant="contained"
              color="primary"
              size="large"
              onClick={handleGeneratePoem}
              disabled={loading || !prompt}
              sx={{ px: 4, py: 1 }}
            >
              {loading ? <CircularProgress size={24} /> : 'إنشاء القصيدة'}
            </Button>
          </Grid>
        </Grid>
      </Paper>
      
      {generatedPoem && (
        <Card elevation={4} sx={{ mb: 4, position: 'relative' }}>
          <CardContent sx={{ p: 4 }}>
            <Box sx={{ position: 'absolute', top: 8, left: 8, display: 'flex' }}>
              <IconButton onClick={handleCopyPoem} title="نسخ القصيدة">
                <ContentCopyIcon />
              </IconButton>
              <IconButton onClick={handleSavePoem} title="حفظ القصيدة" color="primary">
                <SaveIcon />
              </IconButton>
            </Box>
            
            <Typography variant="subtitle2" color="textSecondary" gutterBottom textAlign="center">
              تم الإنشاء باستخدام نموذج: {generatedPoem.model}
            </Typography>
            
            <Typography variant="h5" component="h2" gutterBottom textAlign="center" sx={{ mb: 3, fontWeight: 'bold' }}>
              {generatedPoem.title}
            </Typography>
            
            <Typography
              variant="body1"
              component="pre"
              sx={{
                whiteSpace: 'pre-wrap',
                lineHeight: 1.8,
                fontFamily: 'inherit',
                fontSize: '1.1rem',
                textAlign: 'center',
                direction: 'rtl'
              }}
            >
              {generatedPoem.text}
            </Typography>
          </CardContent>
        </Card>
      )}
    </Box>
  );
};

export default PoemGenerator;
